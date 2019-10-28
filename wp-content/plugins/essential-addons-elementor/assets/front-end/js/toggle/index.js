/*=================================*/
/* 02. Toggle Handler
/*=================================*/
var ToggleHandler = function($scope, $) {
	var toggle_elem = $scope.find(".eael-toggle-container").eq(0);
	$(toggle_elem).each(function() {
		var $toggle_target = $(this).data("toggle-target");
		var $toggle_switch = $($toggle_target).find(".eael-toggle-switch");
		$($toggle_target)
			.find(".eael-primary-toggle-label")
			.addClass("active");
		$($toggle_switch).toggle(
			function() {
				var $parent_container = $(this).closest(
					".eael-toggle-container"
				);
				$($parent_container)
					.find(".eael-toggle-content-wrap")
					.removeClass("primary");
				$($parent_container)
					.children(".eael-toggle-content-wrap")
					.addClass("secondary");
				$($parent_container)
					.find(".eael-toggle-switch-container")
					.addClass("eael-toggle-switch-on");
				$(this)
					.parent()
					.parent()
					.find(".eael-primary-toggle-label")
					.removeClass("active");
				$(this)
					.parent()
					.parent()
					.find(".eael-secondary-toggle-label")
					.addClass("active");
			},
			function() {
				var $parent_container = $(this).closest(
					".eael-toggle-container"
				);
				$($parent_container)
					.children(".eael-toggle-content-wrap")
					.addClass("primary");
				$($parent_container)
					.children(".eael-toggle-content-wrap")
					.removeClass("secondary");
				$($parent_container)
					.find(".eael-toggle-switch-container")
					.removeClass("eael-toggle-switch-on");
				$(this)
					.parent()
					.parent()
					.find(".eael-primary-toggle-label")
					.addClass("active");
				$(this)
					.parent()
					.parent()
					.find(".eael-secondary-toggle-label")
					.removeClass("active");
			}
		);
	});
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-toggle.default",
		ToggleHandler
	);
});
