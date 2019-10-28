(function($) {
	'use strict';

	window.eaelLoadMore = function (options, settings) {

		console.log('options', options);
		console.log('settings', settings);

		// Default Values for Load More Js
		var optionsValue = {
			totalPosts: options.totalPosts,
			loadMoreBtn: options.loadMoreBtn,
			postContainer: $(options.postContainer),
			postStyle: options.postStyle, // block, grid, timeline, 
		}

		// Settings Values
		var settingsValue = {
			postType: settings.postType,
			perPage: settings.perPage,
			postOrder: settings.postOrder,
			orderBy: settings.orderBy,
			showImage: settings.showImage,
			showTitle: settings.showTitle,
			showExcerpt: settings.showExcerpt,
			showMeta: settings.showMeta,
			imageSize: settings.imageSize,
			metaPosition: settings.metaPosition,
			excerptLength: settings.excerptLength,
			btnText: settings.btnText,

			tax_query: settings.tax_query,

			post__in: settings.post__in,
			excludePosts: settings.exclude_posts,
			offset: parseInt( settings.offset , 10 ),
			grid_style: settings.grid_style || '',
			hover_animation: settings.hover_animation,
			show_read_more_button: settings.show_read_more_button,
			read_more_button_text: settings.read_more_button_text
		}

		var offset = settingsValue.offset + settingsValue.perPage;

		optionsValue.loadMoreBtn.on( 'click', function( e ) {
			e.preventDefault();

			$(this).addClass( 'button--loading' );
			$(this).find( 'span' ).html( 'Loading...' );

			$.ajax( {
				url: localize.ajaxurl,
				type: 'post',
				data: {
					action: 'load_more',
					post_style: optionsValue.postStyle,
					eael_show_image : settingsValue.showImage,
					image_size : settingsValue.imageSize,
					eael_show_title : settingsValue.showTitle,
					eael_show_meta : settingsValue.showMeta,
					meta_position : settingsValue.metaPosition,
					
					eael_show_excerpt : settingsValue.showExcerpt,
					eael_excerpt_length : settingsValue.excerptLength,

					post_type: settingsValue.postType,
					posts_per_page : settingsValue.perPage,
					offset : offset,

					tax_query: settingsValue.tax_query,

					post__not_in: settingsValue.excludePosts,
					
					post__in: settingsValue.post__in,

					orderby: settingsValue.orderBy,
					order: settingsValue.postOrder,
					grid_style: settingsValue.grid_style,
					eael_post_block_hover_animation: settingsValue.hover_animation,
					eael_post_grid_hover_animation: settingsValue.hover_animation,
					eael_show_read_more_button: settingsValue.show_read_more_button,
					read_more_button_text: settingsValue.read_more_button_text
				},
				beforeSend: function() {
					// _this.html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Saving Data..');
				},
				success: function( response ) {
					var $content = $(response);

					if (optionsValue.postStyle === 'grid') {
						setTimeout(function () {
							optionsValue.postContainer.masonry();
							optionsValue.postContainer.append( $content ).masonry( 'appended', $content );
							optionsValue.postContainer.masonry({
						    	itemSelector: '.eael-grid-post',
						    	percentPosition: true,
						    	columnWidth: '.eael-post-grid-column'
							});
						
						}, 100);
					} else {
						optionsValue.postContainer.append( $content );
					}
					optionsValue.loadMoreBtn.removeClass( 'button--loading' );
					optionsValue.loadMoreBtn.find( 'span' ).html( settingsValue.btnText );

					offset = offset + settingsValue.perPage;

					if( offset >= optionsValue.totalPosts ) {
						optionsValue.loadMoreBtn.remove();
					}

				},
				error: function() {
					
				}
			} );
		} );
	}; // end of eaelLoadMore

	window.eaelDynamicGalleryLoadMore = function(options, settings, gallery) {
		
		var offset = settings.offset + settings.perPage;

		options.loadMoreBtn.on('click', function(e) {
			e.preventDefault();

			$(this).addClass( 'button--loading' );
			$(this).find( 'span' ).html( 'Loading...' );

			$.ajax({
				url: localize.ajaxurl,
				type: 'post',
				data: {
					action: 'load_more',
					post_type: settings.postType,
					posts_per_page : settings.perPage,
					post_style: options.postStyle,
					offset : offset,
					tax_query: settings.tax_query,
					post__not_in: settings.exclude_posts,
					post__in: settings.post__in,
					orderby: settings.orderBy,
					eael_fg_grid_style: options.gridStyle,
					eael_fg_grid_hover_style:options.hoverStyle,
					eael_fg_show_popup: options.popUp,
					eael_section_fg_zoom_icon: options.zoomIcon,
					eael_section_fg_link_icon: options.linkIcon,
					order: settings.postOrder,
					eael_post_excerpt: settings.postExcerpt,
					eael_fg_show_popup_styles: options.showPopupStyles
				},
				success: function( response ) {
					var $content = $( response );

					setTimeout(function() {
						gallery.isotope('insert', $content);
						options.loadMoreBtn.removeClass( 'button--loading' );
						options.loadMoreBtn.find( 'span' ).html( settings.btnText );
					}, 100);

					offset = offset + settings.perPage;
					if( offset >= options.totalPosts ) {
						options.loadMoreBtn.remove();
					}
				},
				error: function(response) {
					console.log(response);
				}
			});


		});
		


	}



})(jQuery);