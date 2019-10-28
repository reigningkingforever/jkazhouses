/*=================================*/
/* 38. Advanced Menu
	/*=================================*/
var AdvancedMenu = function($scope, $) {
	var $indicator_class = $(".eael-advanced-menu-container", $scope).data(
		"indicator-class"
	);
	var $dropdown_indicator_class = $(
		".eael-advanced-menu-container",
		$scope
	).data("dropdown-indicator-class");
	var $horizontal = $(".eael-advanced-menu", $scope).hasClass(
		"eael-advanced-menu-horizontal"
	);

	if ($horizontal) {
		// insert indicator
		$(".eael-advanced-menu > li.menu-item-has-children", $scope).each(
			function() {
				$("> a", $(this)).append(
					'<span class="' + $indicator_class + '"></span>'
				);
			}
		);
		$(".eael-advanced-menu > li ul li.menu-item-has-children", $scope).each(
			function() {
				$("> a", $(this)).append(
					'<span class="' + $dropdown_indicator_class + '"></span>'
				);
			}
		);

		// insert responsive menu toggle, text
		$(".eael-advanced-menu-horizontal", $scope)
			.before('<span class="eael-advanced-menu-toggle-text"></span>')
			.after(
				'<button class="eael-advanced-menu-toggle"><span class="eicon-menu-bar"></span></button>'
			);

		// responsive menu slide
		$(".eael-advanced-menu-container", $scope).on(
			"click",
			".eael-advanced-menu-toggle",
			function(e) {
				e.preventDefault();
				$(this)
					.siblings(".eael-advanced-menu-horizontal")
					.css("display") == "none"
					? $(this)
							.siblings(".eael-advanced-menu-horizontal")
							.slideDown(300)
					: $(this)
							.siblings(".eael-advanced-menu-horizontal")
							.slideUp(300);
			}
		);

		// clear responsive props
		$(window).on("resize load", function() {
			if (window.matchMedia("(max-width: 991px)").matches) {
				$(".eael-advanced-menu-horizontal", $scope).addClass(
					"eael-advanced-menu-responsive"
				);
				$(".eael-advanced-menu-toggle-text", $scope).text(
					$(
						".eael-advanced-menu-horizontal .current-menu-item a",
						$scope
					)
						.eq(0)
						.text()
				);
			} else {
				$(".eael-advanced-menu-horizontal", $scope).removeClass(
					"eael-advanced-menu-responsive"
				);
				$(
					".eael-advanced-menu-horizontal, .eael-advanced-menu-horizontal ul",
					$scope
				).css("display", "");
			}
		});
	}

	$(".eael-advanced-menu > li.menu-item-has-children", $scope).each(
		function() {
			// indicator position
			var $height = parseInt($("a", this).css("line-height")) / 2;
			$(this).append(
				'<span class="eael-advanced-menu-indicator ' +
					$indicator_class +
					'" style="top:' +
					$height +
					'px"></span>'
			);

			// if current, keep indicator open
			// $(this).hasClass('current-menu-ancestor') ? $(this).addClass('eael-advanced-menu-indicator-open') : ''
		}
	);

	$(".eael-advanced-menu > li ul li.menu-item-has-children", $scope).each(
		function(e) {
			// indicator position
			var $height = parseInt($("a", this).css("line-height")) / 2;
			$(this).append(
				'<span class="eael-advanced-menu-indicator ' +
					$dropdown_indicator_class +
					'" style="top:' +
					$height +
					'px"></span>'
			);

			// if current, keep indicator open
			// $(this).hasClass('current-menu-ancestor') ? $(this).addClass('eael-advanced-menu-indicator-open') : ''
		}
	);

	// menu indent
	$(
		".eael-advanced-menu-dropdown-align-left .eael-advanced-menu-vertical li.menu-item-has-children"
	).each(function() {
		var $padding_left = parseInt($("a", $(this)).css("padding-left"));

		$("ul li a", this).css({
			"padding-left": $padding_left + 20 + "px"
		});
	});

	$(
		".eael-advanced-menu-dropdown-align-right .eael-advanced-menu-vertical li.menu-item-has-children"
	).each(function() {
		var $padding_right = parseInt($("a", $(this)).css("padding-right"));

		$("ul li a", this).css({
			"padding-right": $padding_right + 20 + "px"
		});
	});

	// menu toggle
	$(".eael-advanced-menu", $scope).on(
		"click",
		".eael-advanced-menu-indicator",
		function(e) {
			e.preventDefault();
			$(this).toggleClass("eael-advanced-menu-indicator-open");
			$(this).hasClass("eael-advanced-menu-indicator-open")
				? $(this)
						.siblings("ul")
						.slideDown(300)
				: $(this)
						.siblings("ul")
						.slideUp(300);
		}
	);
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.default",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-one",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-two",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-three",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-four",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-five",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-six",
		AdvancedMenu
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-advanced-menu.skin-seven",
		AdvancedMenu
	);
});
