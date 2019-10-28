/*=================================*/
/* 27. MailChimp
/*=================================*/
var MailChimp = function($scope, $) {
	var $mailChimp = $scope.find(".eael-mailchimp-wrap").eq(0),
		$mailchimp_id =
			$mailChimp.data("mailchimp-id") !== undefined
				? $mailChimp.data("mailchimp-id")
				: "",
		$api_key =
			$mailChimp.data("api-key") !== undefined
				? $mailChimp.data("api-key")
				: "",
		$list_id =
			$mailChimp.data("list-id") !== undefined
				? $mailChimp.data("list-id")
				: "",
		$button_text =
			$mailChimp.data("button-text") !== undefined
				? $mailChimp.data("button-text")
				: "",
		$success_text =
			$mailChimp.data("success-text") !== undefined
				? $mailChimp.data("success-text")
				: "",
		$loading_text =
			$mailChimp.data("loading-text") !== undefined
				? $mailChimp.data("loading-text")
				: "";

	eael_mailchimp_subscribe(
		"eael-mailchimp-form-" + $mailchimp_id + "",
		$api_key,
		$list_id,
		$button_text,
		$success_text,
		$loading_text
	);
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-mailchimp.default",
		MailChimp
	);
});
