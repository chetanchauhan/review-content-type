(function ($) {

	function updateInputRows(el) {
		var inputRows = el.find('.rct-field-input-row');
		inputRows.find('input').each(function (index) {
			var id = $(this).attr('id');
			id = id.replace(/(-\d+)$/, '-' + index);
			$(this).attr('id', id);
		});
	}

	$(function () {
		$('#rct-permalink-settings-selection').find('input').change(function () {
			if ('' === this.value) {
				$('#rct-permalink-settings-review-base').removeAttr('readonly').val(this.value).trigger('focus');
			} else {
				$('#rct-permalink-settings-review-base').attr('readonly', 'readonly').val(this.value);
			}
		});

		$('.rct-fields-wrapper').on('click', '.rct-add-input,.rct-remove-input', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var target = $(e.target).closest('.rct-field-input-row'),
				update = target.parent();

			if ($(e.target).hasClass('rct-add-input')) {
				target.clone().insertAfter(target).find('input').val('');
				updateInputRows(update);
			} else if ($(e.target).hasClass('rct-remove-input')) {
				if (target.siblings('.rct-field-input-row').length > 0) {
					target.remove();
					updateInputRows(update);
				}
			}
		});

		if(typeof $.fn.sortable === 'function' ){
			$('.rct-repeatable').sortable({
				axis: 'y',
				cursor: 'move',
				items: '.rct-field-input-row',
				handle: '.rct-sorthandle',
				forceHelperSize: true,
				tolerance: 'pointer',
				update: function (e) {
					updateInputRows($(e.target));
				}
			});
		}

	});
}(jQuery));
