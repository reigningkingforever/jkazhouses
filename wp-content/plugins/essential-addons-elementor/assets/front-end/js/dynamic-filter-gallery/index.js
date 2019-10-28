var DynamicFilterableGallery = function($scope, $) {
    var $gallery	= $scope.find('.eael-filter-gallery-container').eq(0),
        $galleryWrap = $scope.find('.eael-filter-gallery-wrapper').eq(0),
        $settings	= $gallery.data('settings');

        // if( !isEditMode ) {
        if( true ) {
            var $layout_mode = 'fitRows';

            if( 'masonry' == $settings.layout_mode ) {
                $layout_mode = 'masonry';
            }

            var $isotope_args = {
                itemSelector:   '.dynamic-gallery-item',
                layoutMode		: $layout_mode,
                percentPosition : true,
                stagger: 30,
                transitionDuration: $settings.duration + 'ms',
            }

            var $isotope_gallery = {};
            
            $scope.imagesLoaded(function(e) {
                $isotope_gallery = $gallery.isotope($isotope_args);
            });
            
            $scope.on('click', '.control', function() {
                var $this       = $(this),
                    filterValue = $this.attr('data-filter');

                $this.siblings().removeClass('active');
                $this.addClass('active');
                $isotope_gallery.isotope({ filter: filterValue });
            });

        var $gallery_id        = $galleryWrap.data('gallery_id'),
            $gallery_by_id     = $('#eael-filter-gallery-wrapper-'+$gallery_id),
            $post_style        = $galleryWrap.data('post_style'),
            $grid_style        = $galleryWrap.data('grid_style'),
            $grid_hover_style  = $galleryWrap.data('grid_hover_style'),
            $show_popup        = $galleryWrap.data('show_popup'),
            $show_popup_styles = $galleryWrap.data('show_popup_styles'),
            $zoom_icon         = $galleryWrap.data('zoom_icon'),
            $link_icon         = $galleryWrap.data('link_icon'),
            $post_excerpt      = $galleryWrap.data('post_excerpt'),
            $btn_text          = $galleryWrap.data('btn_text'),
            $total_posts       = $galleryWrap.data('total_posts'),
            $post_type         = $galleryWrap.data('post_type'),
            $posts_per_page    = $galleryWrap.data('posts_per_page'),
            $post_order        = $galleryWrap.data('post_order'),
            $post_orderby      = $galleryWrap.data('post_orderby'),
            $post_offset       = $galleryWrap.data('post_offset'),
            $tax_query         = $galleryWrap.data('tax_query'),
            $exclude_posts     = $galleryWrap.data('exclude_posts'),
            $post__in          = $galleryWrap.data('post__in');

            var options = {
                totalPosts     : $total_posts,
                postStyle      : $post_style,
                loadMoreBtn    : $( '#eael-load-more-btn-' + $gallery_id ),
                postContainer  : $( '.eael-filter-gallery-appender-' + $gallery_id ),
                gridStyle      : $grid_style,
                hoverStyle     : $grid_hover_style,
                popUp          : $show_popup,
                showPopupStyles: $show_popup_styles,
                zoomIcon       : $zoom_icon,
                linkIcon       : $link_icon
            }

            var gallerySettings = {
                postType     : $post_type,
                perPage      : $posts_per_page,
                postOrder    : $post_order,
                orderBy      : $post_orderby,
                offset       : $post_offset,
                tax_query    : $tax_query,
                exclude_posts: $exclude_posts,
                post__in     : $post__in,
                postExcerpt  : $post_excerpt,
                btnText      : $btn_text
            }

            eaelDynamicGalleryLoadMore( options, gallerySettings, $gallery);
        }

}

jQuery(window).on("elementor/frontend/init", function() {
    elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-dynamic-filterable-gallery.default",
		DynamicFilterableGallery
	);
});
