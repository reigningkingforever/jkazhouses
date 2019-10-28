/*=================================*/
/* 35. Pricing Tooltip
/*=================================*/
var EaelOffcanvas = function($scope, $) {
	new window.EAELOffcanvasContent($scope);
};
jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-offcanvas.default",
		EaelOffcanvas
	);
});
