<?php
/**
 * Allow switching the listing package.
 *
 * @since 1.0
 */

namespace MyListing\Src\Paid_Listings\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Switch_Package {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_filter( 'mylisting/submission-steps', [ $this, 'submission_steps' ], 150 );
		add_filter( 'job_manager_my_job_actions', [ $this, 'add_switch_plan_button' ], 10, 2 );

		// fires after the subscription has been activated, and the payment package has been created
		add_action( 'mylisting/subscriptions/switch/order-processed', [ $this, 'subscription_processed' ], 10, 2 );

		// fires after the order has been paid and processed and the payment package has been created
		add_action( 'mylisting/payments/switch/order-processed', [ $this, 'order_processed' ], 10, 2 );

		// fires after a package for switch/relist has been chosen, before the user is redirected to checkout
		add_action( 'mylisting/payments/switch/product-selected', [ $this, 'product_selected' ], 10, 2 );

		// if the users is switching to a pre-owned package, assign it to the listing
		add_action( 'mylisting/payments/switch/use-available-package', [ $this, 'use_available_package' ], 10, 2 );

		// if `skip-checkout` has been configured for a free package, bypass the cart and create the payment package
		add_action( 'mylisting/payments/switch/use-free-package', [ $this, 'use_free_package' ], 10, 2 );
	}

	public function submission_steps( $steps ) {
		$actions = [ 'switch', 'relist' ];
		if ( empty( $_GET['action'] ) || ! in_array( $_GET['action'], $actions ) ) {
			return $steps;
		}

		return [ 'switch-package' => [
			'name'     => _x( 'Choose a package', 'Switch package', 'my-listing' ),
			'view'     => [ $this, 'choose_package' ],
			'handler'  => [ $this, 'choose_package_handler' ],
			'priority' => 5,
		] ];
	}

	/**
	 * Choose Package View
	 *
	 * @since 1.6
	 */
	public function choose_package() {
		if ( empty( $_REQUEST['listing_type'] ) || ! ( $type = \MyListing\Ext\Listing_Types\Listing_Type::get_by_name( $_REQUEST['listing_type'] ) ) ) {
			return;
		}

		$form = \MyListing\Src\Forms\Add_Listing_Form::instance();
		$tree = \MyListing\Src\Paid_Listings\Util::get_package_tree_for_listing_type( $type );
		$action = ! empty( $_GET['action'] ) ? $_GET['action'] : '';
		$listing_id = ! empty( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : $form->get_job_id();
		$listing = \MyListing\Src\Listing::get( $listing_id );
		if ( ! $listing ) {
			return;
		}

		$title = ( $action === 'relist' )
			? _x( 'Relist', 'Switch Package', 'my-listing' )
			: _x( 'Switch plan for listing', 'Switch Package', 'my-listing' );

		$description = ( $action === 'relist' )
			? _x( 'Previous plan:', 'Switch Package', 'my-listing' )
			: _x( 'Current plan:', 'Switch Package', 'my-listing' );
		?>
		<section class="i-section c27-packages">
			<div class="container">
				<div class="row section-title">
					<h2 class="case27-primary-text">
						<?php echo $title ?>
						<?php printf( ' "<a href="%s" target="_blank">%s</a>"', esc_url( $listing->get_link() ), $listing->get_name() ) ?>
					</h2>

					<?php if ( ( $current_package = $listing->get_package() ) && ( $current_product = $current_package->get_product() ) ): ?>
						<?php printf(
							'%s <a href="%s" title="%s" target="_blank">%s</a>.',
							$description,
							esc_url( $current_product->get_permalink() ),
							esc_attr( sprintf( _x( 'Package #%d', 'Switch Package', 'my-listing' ), $current_package->get_id() ) ),
							$current_product->get_title()
						) ?>
					<?php endif ?>
				</div>
				<form method="post" id="job_package_selection">
					<div class="job_listing_packages">

						<?php require locate_template( 'templates/add-listing/choose-package.php' ) ?>

						<div class="hidden">
							<input type="hidden" name="job_id" value="<?php echo esc_attr( $listing_id ) ?>">
							<input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ) ?>">
							<input type="hidden" name="job_manager_form" value="<?php echo esc_attr( $form->form_name ) ?>">
							<?php if ( ! empty( $_REQUEST['listing_type'] ) ): ?>
								<input type="hidden" name="listing_type" value="<?php echo esc_attr( $_REQUEST['listing_type'] ) ?>">
							<?php endif ?>
						</div>
					</div>
				</form>
			</div>
		</section>
		<?php
	}

	public function choose_package_handler() {
		$form = \MyListing\Src\Forms\Add_Listing_Form::instance();
		$actions = [ 'switch', 'relist' ];

		try {
			if ( ! is_user_logged_in() || empty( $_GET['action'] ) || ! in_array( $_GET['action'], $actions ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Switch package', 'my-listing' ) );
			}

			if ( empty( $_POST['listing_package'] ) || empty( $_GET['listing'] ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Switch package', 'my-listing' ) );
			}

			$action = $_GET['action'];
			$listing = \MyListing\Src\Listing::get( $_GET['listing'] );

			if ( ! ( $listing && $listing->type && $listing->editable_by_current_user() ) ) {
				throw new \Exception( _x( 'Something went wrong.', 'Switch package', 'my-listing' ) );
			}

			if ( ! \MyListing\Src\Paid_Listings\Util::validate_package( $_POST['listing_package'], $listing->type->get_slug() ) ) {
				throw new \Exception( _x( 'Chosen package is not valid.', 'Switch package', 'my-listing' ) );
			}

			// Package is valid.
			$package = get_post( $_POST['listing_package'] );

			/**
			 * If the user used a payment package to switch listing, assign the package and publish the listing.
			 */
			if ( $package->post_type === 'case27_user_package' ) {
				do_action( 'mylisting/payments/switch/use-available-package', $listing, \MyListing\Src\Package::get( $package ) );
			}

			/**
			 * If the user is buying a new package, add the package to cart and redirect.
			 * If `skip-checkout` is configured, apply the package immediately.
			 */
			if ( $package->post_type === 'product' ) {
				$product = wc_get_product( $package->ID );
				if ( ! ( $product && $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) ) {
					throw new \Exception( _x( 'Invalid product.', 'Listing submission', 'my-listing' ) );
				}

				$skip_checkout = apply_filters( 'mylisting\packages\free\skip-checkout', true ) === true;

				// if `skip-checkout` setting is enabled for free products, create the user package and assign it to the listing
				if ( $product->get_price() == 0 && $skip_checkout && $product->get_meta( '_disable_repeat_purchase' ) !== 'yes' ) {
					do_action( 'mylisting/payments/switch/use-free-package', $listing, $product );
				} else {
					// proceed to checkout
					do_action( 'mylisting/payments/switch/product-selected', $listing, $product );
				}
			}

			// Redirect to user dashboard.
			$message = $action === 'relist'
				? _x( 'Listing has been successfully relisted.', 'Switch Package', 'my-listing' )
				: _x( 'Listing plan has been updated.', 'Switch Package', 'my-listing' );

			wc_add_notice( $message, 'success' );
			wp_safe_redirect( wc_get_account_endpoint_url( 'my-listings' ) );
			exit;
		} catch (\Exception $e) {
			// Log error message.
			$form->add_error( $e->getMessage() );
			$form->set_step( array_search( 'switch-package', array_keys( $form->get_steps() ) ) );
		}
	}

	public function add_switch_plan_button( $actions, $listing ) {
		if ( isset( $actions['relist'] ) ) {
			unset( $actions['relist'] );
		}

		if ( ! ( $listing = \MyListing\Src\Listing::get( $listing ) ) ) {
			return $actions;
		}

		if ( ! in_array( $listing->get_data('post_status'), [ 'publish', 'expired' ] ) ) {
			return $actions;
		}

		if ( ! ( $plans_page = c27()->get_setting( 'general_add_listing_page' ) ) ) {
			return $actions;
		}

		// Paid packages disabled for listing type.
		if ( $listing->type && $listing->type->settings['packages']['enabled'] === false ) {
			return $actions;
		}

		$action = $listing->get_data('post_status') === 'publish' ? 'switch' : 'relist';
		$switch_url = add_query_arg( [
			'action' => $action,
			'listing' => $listing->get_id(),
		], $plans_page );

		$actions['cts_switch'] = [
			'type' => 'plain',
			'content' => sprintf(
				'<li class="cts-listing-action-%s"><a href="%s" class="listing-action-switch">%s</a></li>',
				esc_attr( $action ),
				esc_url( $switch_url ),
				$action === 'switch'
					? _x( 'Switch Plan', 'User listings dashboard', 'my-listing' )
					: _x( 'Relist', 'User listings dashboard', 'my-listing' )
			),
		];

		if ( ! empty( $actions['delete'] ) ) {
			$delete = $actions['delete'];
			unset( $actions['delete'] );
			$actions = $actions + [ 'delete' => $delete ];
		}

		return $actions;
	}

	/**
	 * Fires after a package for switch/relist has been chosen, before
	 * the user is redirected to checkout.
	 *
	 * @since 2.1.6
	 */
	public function product_selected( $listing, $product ) {
		// on relist, update the status from expired to pending_payment
		if ( $listing->get_status() === 'expired' ) {
			wp_update_post( [
				'ID' => $listing->get_id(),
				'post_status' => 'pending_payment',
				'post_date' => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_author' => get_current_user_id(),
			] );
		}

		// add package to cart, and redirect
		$data = [
			'job_id' => $listing->get_id(),
			'assignment_type' => 'switch',
		];

		WC()->cart->add_to_cart( $product->get_id(), 1, '', '', $data );

		// Clear cookie
		wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );

		// Redirect to checkout page
		wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
		exit;
	}

	/**
	 * Fires after the subscription has been activated, and the payment package
	 * has been created. Assign the package and publish listing.
	 *
	 * @since 2.1.6
	 */
	public function subscription_processed( $listing, $package ) {
		wp_update_post( [
			'ID' => $listing->get_id(),
			'post_status' => 'publish',
		] );

		$package->assign_to_listing( $listing->get_id() );
	}

	/**
	 * After the order has been paid and processed and the payment package is
	 * created, update the listing package and publish it.
	 *
	 * @since 2.1.6
	 */
	public function order_processed( $listing, $package ) {
		wp_update_post( [
			'ID' => $listing->get_id(),
			'post_status' => 'publish',
		] );

		$package->assign_to_listing( $listing->get_id() );
	}

	/**
	 * If the user is -switching to- or -relisting using- a pre-owned package, assign it
	 * to the listing and make sure the listing gets published.
	 *
	 * @since 2.1.6
	 */
	public function use_available_package( $listing, $package ) {
		wp_update_post( [
			'ID' => $listing->get_id(),
			'post_status' => 'publish',
		] );

		$package->assign_to_listing( $listing->get_id() );
	}

	/**
	 * If `skip-checkout` has been configured for a free package,
	 * bypass the cart and create the payment package.
	 *
	 * @since 2.1.6
	 */
	public function use_free_package( $listing, $product ) {
		$package = \MyListing\Src\Package::create( [
			'user_id'        => get_current_user_id(),
			'product_id'     => $product->get_id(),
			'duration'       => $product->get_duration(),
			'limit'          => $product->get_limit(),
			'featured'       => $product->is_listing_featured(),
			'mark_verified'  => $product->mark_verified(),
			'use_for_claims' => $product->use_for_claims(),
			'order_id'       => false,
		] );

		if ( ! $package ) {
			throw new \Exception( _x( 'Couldn\'t create package.', 'Listing submission', 'my-listing' ) );
		}

		wp_update_post( [
			'ID' => $listing->get_id(),
			'post_status' => 'publish',
		] );

		$package->assign_to_listing( $listing->get_id() );
	}
}
