/*=================================*/
/* 13. Content Timeline
	/*=================================*/
var contentTimelineHandler = function($scope, $) {
	var contentBlock = $(".eael-content-timeline-block");

	$(window).on("scroll", function() {
		contentBlock.each(function() {
			if ($(this).find(".highlight")) {
				// Calculate screen middle position, top offset and line height and
				// change line height dynamically

				var lineEnd =
					contentBlock.height() * 0.15 + window.innerHeight / 2;
				var topOffset = $(this).offset().top;
				var lineHeight = window.scrollY + lineEnd * 1.3 - topOffset;

				$(this)
					.find(".eael-content-timeline-inner")
					.css("height", lineHeight + "px");
			}
		});

		if (this.oldScroll > this.scrollY == false) {
			this.oldScroll = this.scrollY;
			// Scroll Down
			$(".eael-content-timeline-block.highlight")
				.prev()
				.find(".eael-content-timeline-inner")
				.removeClass("eael-muted")
				.addClass("eael-highlighted");
		} else if (this.oldScroll > this.scrollY == true) {
			this.oldScroll = this.scrollY;
			// Scroll Up
			$(".eael-content-timeline-block.highlight")
				.find(".eael-content-timeline-inner")
				.addClass("eael-prev-highlighted");
			$(".eael-content-timeline-block.highlight")
				.next()
				.find(".eael-content-timeline-inner")
				.removeClass("eael-highlighted")
				.removeClass("eael-prev-highlighted")
				.addClass("eael-muted");
		}
	});
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-content-timeline.default",
		contentTimelineHandler
	);
});
