<?php
/**
 * WC Subscriptions Integrations Setup.
 *
 * @since 1.6
 */

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Subscriptions {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// WC Subscriptions must be enabled to use this feature.
		if ( ! class_exists( '\WC_Subscriptions' ) ) {
			return;
		}

		// Add listing as valid subscription.
		add_filter( 'woocommerce_is_subscription', [ $this, 'woocommerce_is_subscription' ], 10, 2 );

		// Add product type.
		add_filter( 'woocommerce_subscription_product_types', [ $this, 'add_subscription_product_types' ] );
		add_filter( 'product_type_selector', [ $this, 'add_product_type_selector' ] );

		// Product Class.
		add_filter( 'woocommerce_product_class' , [ $this, 'set_product_class' ], 10, 3 );

		// Add to cart.
		add_action( 'woocommerce_job_package_subscription_add_to_cart', '\WC_Subscriptions::subscription_add_to_cart', 30 );

		/* PAYMENTS */

		// Subscription Synchronisation.
		// activate sync (process meta) for listing package.
		if ( class_exists( 'WC_Subscriptions_Synchroniser' ) && method_exists( '\WC_Subscriptions_Synchroniser', 'save_subscription_meta' ) ) {
			add_action( 'woocommerce_process_product_meta_job_package_subscription', '\WC_Subscriptions_Synchroniser::save_subscription_meta', 10 );
		}

		// Prevent listing linked to product(subs) never expire automatically.
		add_action( 'added_post_meta', [ $this, 'updated_post_meta' ], 10, 4 );
		add_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], 10, 4 );

		// When listing expires, adjust user package usage and delete package & user package meta in listing.
		add_action( 'publish_to_expired', [ $this, 'check_expired_listing' ] );

		// Change user package usage when trash/untrash listing.
		add_action( 'wp_trash_post', [ $this, 'wp_trash_post' ] );
		add_action( 'untrash_post', [ $this, 'untrash_post' ] );

		/* === SUBS ENDED. === */

		// Subscription Paused (on Hold).
		add_action( 'woocommerce_subscription_status_on-hold', [ $this, 'subscription_ended' ] );

		// Subscription Ended.
		add_action( 'woocommerce_scheduled_subscription_expiration', [ $this, 'subscription_ended' ] );

		// When a subscription ends after remaining unpaid.
		add_action( 'woocommerce_scheduled_subscription_end_of_prepaid_term', [ $this, 'subscription_ended' ] );

		// When the subscription status changes to cancelled.
		add_action( 'woocommerce_subscription_status_cancelled', [ $this, 'subscription_ended' ] );

		// Subscription is expired.
		add_action( 'woocommerce_subscription_status_expired', [ $this, 'subscription_ended' ] );

		/* === SUBS STARTS. === */

		// Subscription starts ( status changes to active ).
		add_action( 'woocommerce_subscription_status_active', [ $this, 'subscription_activated' ] );

		/* === SUBS RENEWED. === */

		// When the subscription is renewed.
		add_action( 'woocommerce_subscription_renewal_payment_complete', [ $this, 'subscription_renewed' ] );
	}

	/**
	 * Is this a subscription product?
	 *
	 * @since 1.6
	 */
	public function woocommerce_is_subscription( $is_subscription, $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( [ 'job_package_subscription' ] ) ) {
			$is_subscription = true;
		}
		return $is_subscription;
	}

	/**
	 * Types for subscriptions.
	 *
	 * @since 1.6
	 */
	public function add_subscription_product_types( $types ) {
		$types[] = 'job_package_subscription';
		return $types;
	}

	/**
	 * Add the product type selector.
	 *
	 * @since 1.6
	 */
	public function add_product_type_selector( $types ) {
		$types['job_package_subscription'] = __( 'Listing Subscription', 'my-listing' );
		return $types;
	}

	/**
	 * Set Product Class to Load.
	 *
	 * @since 1.6
	 * @param string $classname Current classname found.
	 * @param string $product_type Current product type.
	 */
	public function set_product_class( $classname, $product_type ) {
		if ( $product_type === 'job_package_subscription' ) {
			$classname = '\MyListing\Src\Paid_Listings\Product_Subscription';
		}

		return $classname;
	}

	/**
	 * Get subscription type for pacakge by ID.
	 *
	 * @since 1.6
	 */
	public function get_package_subscription_type( $product_id ) {
		$subscription_type = get_post_meta( $product_id, '_package_subscription_type', true );
		return empty( $subscription_type ) ? 'package' : $subscription_type;
	}

	/**
	 * Prevent listings linked to subscriptions from expiring.
	 *
	 * @since 1.6
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( 'job_listing' === get_post_type( $object_id ) && '' !== $meta_value && '_job_expires' === $meta_key ) {
			$_package_id = get_post_meta( $object_id, '_package_id', true );
			$package     = wc_get_product( $_package_id );

			if ( $package && 'job_package_subscription' === $package->get_type() && 'listing' === $package->get_package_subscription_type() ) {
				update_post_meta( $object_id, '_job_expires', '' ); // Never expire automatically.
			}
		}
	}

	/**
	 * When listings expire, we may have to change the listing count
	 * for the package, based on the subscription type.
	 *
	 * @since 1.6
	 */
	public function check_expired_listing( $post ) {
		$listing = \MyListing\Src\Listing::get( $post );
		if ( ! $listing ) {
			return;
		}

		$package = $listing->get_package();
		if ( ! $package ) {
			return;
		}

		$subscription_type = $this->get_package_subscription_type( $package->get_product_id() );

		/**
		 * If this is a `listing` subscription type, the user should gain back a slot
		 * after a listing expires.
		 */
		if ( $subscription_type === 'listing' ) {
			$package->decrease_count();
			delete_post_meta( $listing->get_id(), '_package_id' );
			delete_post_meta( $listing->get_id(), '_user_package_id' );
		}
	}

	/**
	 * If a listing gets trashed/deleted, the pack may need it's listing count changing.
	 *
	 * @since 1.6
	 */
	public function wp_trash_post( $post_id ) {
		$listing = \MyListing\Src\Listing::get( $post_id );
		if ( ! ( $listing && $listing->get_package() ) ) {
			return;
		}

		// pending-to-trash cases are handled by User_Packages class.
		if ( $listing->get_status() === 'pending' ) {
			return;
		}

		$package = $listing->get_package();
		$subscription_type = $this->get_package_subscription_type( $package->get_product_id() );

		/**
		 * If this is a `listing` subscription type, the user should gain back a slot
		 * on their payment package if a listing is trashed/deleted.
		 */
		if ( $subscription_type === 'listing' ) {
			$package->decrease_count();
		}
	}

	/**
	 * If a listing gets restored, the pack may need it's listing count changing.
	 *
	 * @since 1.6
	 */
	public function untrash_post( $post_id ) {
		$listing = \MyListing\Src\Listing::get( $post_id );
		if ( ! ( $listing && $listing->get_package() ) ) {
			return;
		}

		// if the status the listing will transition to after it gets untrashed is `pending`,
		// return early as that will be handled by User_Packages class.
		$post_status = get_post_meta( $listing->get_id(), '_wp_trash_meta_status', true );
		if ( $post_status === 'pending' ) {
			return;
		}

		$package = $listing->get_package();
		$subscription_type = $this->get_package_subscription_type( $package->get_product_id() );

		/**
		 * If this is a `listing` subscription type, the user should lose a slot
		 * on their payment package if a listing is restored after getting trashed/deleted.
		 */
		if ( $subscription_type === 'listing' ) {
			$package->increase_count();
		}
	}

	/**
	 * Subscription has expired - cancel listing packs.
	 *
	 * @since 1.6
	 */
	public function subscription_ended( $subscription ) {
		if ( is_int( $subscription ) ) {
			$subscription = wcs_get_subscription( $subscription );
		}

		foreach ( $subscription->get_items() as $item ) {
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );

			$packages = get_posts( [
				'post_type' => 'case27_user_package',
				'post_status' => [ 'publish', 'case27_full' ],
				'posts_per_page' => 1,
				'suppress_filters' => false,
				'fields' => 'ids',
				'meta_query' => [
					'relation' => 'AND',
					[ 'key' => '_order_id', 'value' => $subscription->get_id() ],
					[ 'key' => '_product_id', 'value' => $item['product_id'] ],
				],
			] );

			// validate package id
			if ( ! is_array( $packages ) || empty( $packages ) ) {
				continue;
			}

			$package_id = $packages[0];

			/**
			 * If this is a `listing` subscription type, the package should
			 * be deleted when the subscription ends.
			 */
			if ( $subscription_type === 'listing' ) {

				// Delete the package.
				wp_delete_post( $package_id, true ); // @todo:maybe force delete.
				$listing_ids = get_posts( [
					'post_type'      => 'job_listing',
					'post_status'    => [ 'publish', 'pending' ],
					'fields'         => 'ids',
					'posts_per_page' => -1,
					'meta_key'       => '_user_package_id',
					'meta_value'     => $package_id,
				] );

				foreach ( $listing_ids as $listing_id ) {
					$old_status = get_post_status( $listing_id );
					wp_update_post( [
						'ID' => $listing_id,
						'post_status' => 'expired',
					] );

					// Make a record of the subscription ID in case of re-activation.
					update_post_meta( $listing_id, '_expired_subscription_id', $subscription->get_id() );

					// Also save the old listing status.
					update_post_meta( $listing_id, '_expired_subscription_status', $old_status );
				}
			}

			/**
			 * Otherwise, if this is a `package` subscription type, the user gets to keep their package in
			 * it's current state. However, it's listing counts won't be renewed anymore.
			 */
			if ( $subscription_type === 'package' ) {
				// ...
			}
		}

		// delete this flag so the package can be processed again if it gets reactivated
		delete_post_meta( $subscription->get_id(), 'wc_paid_listings_subscription_packages_processed' );
		mlog('end');
	}

	/**
	 * Subscription activated.
	 *
	 * @since 1.6
	 */
	public function subscription_activated( $subscription ) {
		global $wpdb;

		if ( get_post_meta( $subscription->get_id(), 'wc_paid_listings_subscription_packages_processed', true ) ) {
			return;
		}

		foreach ( $subscription->get_items() as $item ) {
			$package = false;
			$product = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );

			// validate subscription package
			if ( isset( $item['switched_subscription_item_id'] ) || ! ( $product->is_type( 'job_package_subscription' ) && $subscription->get_user_id() ) ) {
				continue;
			}

			// if this is a reactivation, get the previous package
			$current_package = get_posts( [
				'post_type' => 'case27_user_package',
				'post_status' => [ 'publish', 'case27_full' ],
				'posts_per_page' => 1,
				'suppress_filters' => false,
				'fields' => 'ids',
				'meta_query' => [
					'relation' => 'AND',
					[ 'key' => '_order_id', 'value' => $subscription->get_id() ],
					[ 'key' => '_product_id', 'value' => $item['product_id'] ],
				],
			] );
			$current_package_id = is_array( $current_package ) && ! empty( $current_package ) ? $current_package[0] : false;

			/**
			 * Handle `listing` subscription types. This type of subscriptions works by tying its listings
			 * expiry date to that of the subscription itself.
			 */
			if ( $subscription_type === 'listing' ) {

				/**
				 * For `listing` subscription types, this should've been deleted already when the
				 * subscription ended, but it's possible it didn't in earlier theme versions, so
				 * we do it again here to be sure (for `listing` subscription types only).
				 */
				if ( $current_package_id ) {
					wp_delete_post( $current_package_id, true );
				}

				// always create a new payment package
				$package = \MyListing\Src\Package::create( [
					'user_id'        => $subscription->get_user_id(),
					'order_id'       => $subscription->get_id(),
					'product_id'     => $product->get_id(),
					'duration'       => $product->get_duration(),
					'limit'          => $product->get_limit(),
					'featured'       => $product->is_listing_featured(),
					'mark_verified'  => $product->mark_verified(),
					'use_for_claims' => $product->use_for_claims(),
				] );

				if ( ! $package ) {
					continue;
				}

				/**
				 * If this is a re-activation of the subscription, get previously
				 * expired listing ids and re-publish them.
				 */
				$expired_ids = (array) $wpdb->get_col( $wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s", '_expired_subscription_id',
					$subscription->get_id()
				) );
				$expired_ids = array_unique( array_filter( array_map( 'absint', $expired_ids ) ) );
				foreach ( $expired_ids as $listing_id ) {
					if ( ! in_array( get_post_status( $listing_id ), [ 'pending_payment', 'expired' ], true ) ) {
						continue;
					}

					// get the listing status before it expired (in case it was set to pending instead of publish)
					$old_status = get_post_meta( $listing_id, '_expired_subscription_status', true );

					// remove expired subscription metadata, no longer needed
					delete_post_meta( $listing_id, '_expired_subscription_id' );
					delete_post_meta( $listing_id, '_expired_subscription_status' );

					// update the package id in the listing meta
					update_post_meta( $listing_id, '_user_package_id', $package->get_id() );

					// update expiry date (never expire for `listing` subscription type)
					update_post_meta( $listing_id, '_job_expires', '' );

					// determine the listing status upon re-activation
					$new_status = in_array( $old_status, [ 'pending', 'publish' ], true )
						? $old_status
						: ( mylisting_get_setting( 'submission_requires_approval' ) ? 'pending' : 'publish' );

					// re-activate listing
					wp_update_post( [
						'ID' => $listing_id,
						'post_status' => $new_status,
					] );

					// update package counts
					$package->increase_count();
				}
			}

			/**
			 * Handle `package` subscription types. This type of subscriptions works by creating one
			 * package, and resets it's listing count to zero on every renewal.
			 */
			if ( $subscription_type === 'package' ) {

				/**
				 * If this is a re-activation of the subscription, see if we already
				 * have created a package, and use that instead.
				 */
				$package = \MyListing\Src\Package::get( $current_package_id );

				// otherwise, create a new package
				if ( ! $package ) {
					$package = \MyListing\Src\Package::create( [
						'user_id'        => $subscription->get_user_id(),
						'order_id'       => $subscription->get_id(),
						'product_id'     => $product->get_id(),
						'duration'       => $product->get_duration(),
						'limit'          => $product->get_limit(),
						'featured'       => $product->is_listing_featured(),
						'mark_verified'  => $product->mark_verified(),
						'use_for_claims' => $product->use_for_claims(),
					] );

					if ( ! $package ) {
						continue;
					}
				}
			}

			/**
			 * If a listing id has been passed to the subscription order (through Add Listing,
			 * Claim Listing, or Switch Package forms), handle it.
			 */
			$listing_id = ! empty( $item['job_id'] ) ? absint( $item['job_id'] ) : false;
			$listing = \MyListing\Src\Listing::get( $listing_id );
			$assignment_type = ! empty( $item['assignment_type'] ) ? $item['assignment_type'] : 'submission';
			if ( ! empty( $item['is_claim'] ) ) {
				$assignment_type = 'claim'; // backwards compatibility pre v2.1.3
			}

			if ( $listing && $package ) {

				/**
				 * The submitted listing should only be processed the first time the subscription is activated. If it gets
				 * reactivated, the listing will already be handled at this point through it's `_expired_subscription_id`
				 * and `_expired_subscription_status` meta keys which are set when a subscription ends.
				 *
				 * @since 2.1.6
				 */
				if ( get_post_meta( $subscription->get_id(), 'wc_paid_listings_subscription_listing_processed', true ) ) {
					return;
				}

				// update meta key so this is only run the first time the subscription is activated
				update_post_meta( $subscription->get_id(), 'wc_paid_listings_subscription_listing_processed', true );

				/**
				 * Subscription has been activated, payment package has been created, and a valid
				 * listing has been passed to the order. This can be used by Add Listing and
				 * other forms to modify the listing information, assign the package, etc.
				 *
				 * @since 2.1.6
				 */
				do_action( sprintf( 'mylisting/subscriptions/%s/order-processed', $assignment_type ), $listing, $package );
			}
		}

		update_post_meta( $subscription->get_id(), 'wc_paid_listings_subscription_packages_processed', true );
		mlog('activate');
	}

	/**
	 * Subscription renewed - renew the listing pack.
	 *
	 * @since 1.6
	 */
	public function subscription_renewed( $subscription ) {
		global $wpdb;

		foreach ( $subscription->get_items() as $item ) {
			$package = false;
			$product = wc_get_product( $item['product_id'] );
			$subscription_type = $this->get_package_subscription_type( $item['product_id'] );

			$current_package = get_posts( [
				'post_type' => 'case27_user_package',
				'post_status' => [ 'publish', 'case27_full' ],
				'posts_per_page' => 1,
				'suppress_filters' => false,
				'fields' => 'ids',
				'meta_query' => [
					'relation' => 'AND',
					[ 'key' => '_order_id', 'value' => $subscription->get_id() ],
					[ 'key' => '_product_id', 'value' => $item['product_id'] ],
				],
			] );
			$current_package_id = is_array( $current_package ) && ! empty( $current_package ) ? $current_package[0] : false;

			/**
			 * On subscription renewals, `package` subscription types
			 * have the package count reset to zero.
			 */
			if ( $subscription_type === 'package' ) {

				// get the package created on subscription activation
				$package = \MyListing\Src\Package::get( $current_package_id );

				// if not available, e.g. it was deleted by the admin, re-create it
				if ( ! $package ) {
					$package = \MyListing\Src\Package::create( [
						'user_id'        => $subscription->get_user_id(),
						'order_id'       => $subscription->get_id(),
						'product_id'     => $product->get_id(),
						'duration'       => $product->get_duration(),
						'limit'          => $product->get_limit(),
						'featured'       => $product->is_listing_featured(),
						'mark_verified'  => $product->mark_verified(),
						'use_for_claims' => $product->use_for_claims(),
					] );

					if ( ! $package ) {
						continue;
					}
				}

				// reset the listing count to zero
				$package->reset_count();
			}

			/**
			 * On subscription renewals, `listing` subscription types don't have anything
			 * to process. The listing expiry date is tied to the subscription end date.
			 */
			if ( $subscription_type === 'listing' ) {
				// ...
			}
		}
		mlog('renew');
	}
}
