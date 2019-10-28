/*=================================*/
/* 19. Post Carousel
/*=================================*/
var PostCarouselHandler = function($scope, $) {
	var $postCarousel = $scope.find(".eael-post-carousel").eq(0),
		$autoplay =
			$postCarousel.data("autoplay") !== undefined
				? $postCarousel.data("autoplay")
				: 999999,
		$pagination =
			$postCarousel.data("pagination") !== undefined
				? $postCarousel.data("pagination")
				: ".swiper-pagination",
		$arrow_next =
			$postCarousel.data("arrow-next") !== undefined
				? $postCarousel.data("arrow-next")
				: ".swiper-button-next",
		$arrow_prev =
			$postCarousel.data("arrow-prev") !== undefined
				? $postCarousel.data("arrow-prev")
				: ".swiper-button-prev",
		$items =
			$postCarousel.data("items") !== undefined
				? $postCarousel.data("items")
				: 3,
		$items_tablet =
			$postCarousel.data("items-tablet") !== undefined
				? $postCarousel.data("items-tablet")
				: 3,
		$items_mobile =
			$postCarousel.data("items-mobile") !== undefined
				? $postCarousel.data("items-mobile")
				: 3,
		$margin =
			$postCarousel.data("margin") !== undefined
				? $postCarousel.data("margin")
				: 10,
		$margin_tablet =
			$postCarousel.data("margin-tablet") !== undefined
				? $postCarousel.data("margin-tablet")
				: 10,
		$margin_mobile =
			$postCarousel.data("margin-mobile") !== undefined
				? $postCarousel.data("margin-mobile")
				: 10,
		$effect =
			$postCarousel.data("effect") !== undefined
				? $postCarousel.data("effect")
				: "slide",
		$speed =
			$postCarousel.data("speed") !== undefined
				? $postCarousel.data("speed")
				: 400,
		$loop =
			$postCarousel.data("loop") !== undefined
				? $postCarousel.data("loop")
				: 0,
		$grab_cursor =
			$postCarousel.data("grab-cursor") !== undefined
				? $postCarousel.data("grab-cursor")
				: 0,
		$pause_on_hover =
			$postCarousel.data("pause-on-hover") !== undefined
				? $postCarousel.data("pause-on-hover")
				: "",
		$centeredSlides = $effect == "coverflow" ? true : false;

	var eaelPostCarousel = new Swiper($postCarousel, {
		direction: "horizontal",
		speed: $speed,
		effect: $effect,
		centeredSlides: $centeredSlides,
		slidesPerView: $items,
		spaceBetween: $margin,
		grabCursor: $grab_cursor,
		autoHeight: true,
		loop: $loop,
		autoplay: {
			delay: $autoplay
		},
		pagination: {
			el: $pagination,
			clickable: true
		},
		navigation: {
			nextEl: $arrow_next,
			prevEl: $arrow_prev
		},
		breakpoints: {
			480: {
				slidesPerView: $items_mobile,
				spaceBetween: $margin_mobile
			},
			768: {
				slidesPerView: $items_tablet,
				spaceBetween: $margin_tablet
			}
		}
	});

	if ($autoplay === 0) {
		eaelPostCarousel.autoplay.stop();
	}

	if ($pause_on_hover && $autoplay !== 0) {
		$postCarousel.on("mouseenter", function() {
			eaelPostCarousel.autoplay.stop();
		});
		$postCarousel.on("mouseleave", function() {
			eaelPostCarousel.autoplay.start();
		});
	}
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-post-carousel.default",
		PostCarouselHandler
	);
});
