(function ($) {
	$(function () {
		$('#rct-permalink-settings-selection').find('input').change(function () {
			if ('' === this.value) {
				$('#rct-permalink-settings-review-base').removeAttr('readonly').val(this.value).trigger('focus');
			} else {
				$('#rct-permalink-settings-review-base').attr('readonly', 'readonly').val(this.value);
			}
		});
	});
}(jQuery));
