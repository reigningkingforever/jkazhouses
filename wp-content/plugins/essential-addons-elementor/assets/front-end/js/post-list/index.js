var postListHandler = function($scope, $) {
	var $_this = $scope.find(".eael-post-list-container");
	var advanceLayout = $scope.find(
		".eael-post-list-container.layout-advanced"
	);

	if (advanceLayout.length) {
		window.insMaxHeight = function(selector) {
			var maxHeight = 0;
			$(selector).each(function() {
				var itm = $(this);
				var height = $(itm[0]).outerHeight();

				if (height >= maxHeight) {
					maxHeight = height;
				}
			});

			$(selector).each(function() {
				$(this).css("min-height", maxHeight + "px");
			});
		};

		insMaxHeight(".eael-post-list-title");
	}

	var $_this = $scope.find(".eael-post-list-container"),
		$cat_con = $scope.find(".post-categories");

	$cat_con.children("a").on("click", function(e) {
		$(".post-categories a").removeClass("active");
		$(this).addClass("active");
	});

	var eael_post_list_settings = {
		appender: $($_this.data("appender")),
		post_type: $_this.data("post_type"),
		posts_per_page:
			$_this.data("posts_per_page") !== ""
				? parseInt($_this.data("posts_per_page"), 10)
				: 11,
		post__in: $_this.data("post__in"),
		orderby: $_this.data("orderby"),
		order: $_this.data("order"),
		total_posts: $_this.data("total_posts"),
		offset:
			$_this.data("offset") !== ""
				? parseInt($_this.data("offset"), 10)
				: 0,
		eael_post_list_post_feature_image: $_this.data("show_image"),
		eael_post_list_post_meta: $_this.data("show_meta"),
		eael_post_list_post_title: $_this.data("show_title"),
		eael_post_list_post_excerpt: $_this.data("show_excerpt"),
		eael_post_list_post_excerpt_length: $_this.data("excerpt_length"),
		eael_post_list_featured_area: $_this.data("show_featured_area"),
		featured_posts: $_this.data("featured_posts"),
		eael_post_list_featured_meta: $_this.data("show_featured_meta"),
		eael_post_list_featured_title: $_this.data("show_featured_title"),
		eael_post_list_featured_excerpt: $_this.data("show_featured_excerpt"),
		eael_post_list_featured_excerpt_length: $_this.data(
			"featured_excerpt_length"
		),
		tax_query: $_this.data("tax_query"),
		excluded: $_this.data("excluded"),
		eael_post_list_pagination: $_this.data("show_nav"),
		eael_post_list_pagination_next_icon: $_this.data("next_icon"),
		eael_post_list_pagination_prev_icon: $_this.data("prev_icon"),
		next_btn: $($_this.data("next_btn")),
		prev_btn: $($_this.data("prev_btn")),
		eael_post_list_layout_type: $_this.data("eael_post_list_layout_type"),
		eael_post_list_post_cat: $_this.data("eael_post_list_post_cat"),
		eael_post_list_author_meta: $_this.data("eael_post_list_author_meta")
	};
	eaelLoadMorePostList(eael_post_list_settings);
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-post-list.default",
		postListHandler
	);
});
