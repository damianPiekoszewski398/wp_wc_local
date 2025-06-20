(function ($) {
	"use strict";



	$(document).on('click', '.rpesp-wrapper a.nav-tab', function () {
		var attrHref = $(this).attr('href');
		$(".rpesp-wrapper a.nav-tab").removeClass('nav-tab-active');
		$('.rpesp-form .nav-content').removeClass('active');
		$(this).addClass('nav-tab-active');
		$('.rpesp-form').find(attrHref).addClass('active');
		return false;
	});

	$(document).on("change", ".enableforall", function () {
		if ($(this).val() == 1) {
			$(".esttimeforall").show();
		} else {
			$(".esttimeforall").hide();
		}
	});
	$(document).on("change", ".enablecarrier", function () {
		if ($(this).val() == 1) {
			$(".esttimeforcarrier").show();
			if ($('.hideoutofstock:checked').val() == 1) {
				$(".esttimeforoutof.esttimeforcarrier").hide();
			}
			if ($('.hide_backorder:checked').val() == 1) {
				$(".esttimeforbackorder.esttimeforcarrier").hide();
			}
		} else {
			$(".esttimeforcarrier").hide();
		}


	});
	$(document).on("change", ".display_on_product", function () {
		if ($(this).val() == 1) {
			$(".text_position_product").show();
		} else {
			$(".text_position_product").hide();
		}
	});
	$(document).on("change", ".combine_date", function () {
		if ($(this).val() == 1) {
			$(".combine_date_option").show();
			$(".item_order_text").hide();
		} else {
			$(".combine_date_option").hide();
			$(".item_order_text").show();
		}
	});
	$(document).on("change", ".display_on_product_archive", function () {
		if ($(this).val() == 1) {
			$(".text_position_product_archive").show();
		} else {
			$(".text_position_product_archive").hide();
		}
	});
	$(document).on("change", ".hideoutofstock", function () {
		if ($(this).val() == 1) {
			$(".esttimeforoutof").hide();
		} else {
			$(".esttimeforoutof").show();
			if ($('.enablecarrier:checked').val() == 0) {
				$(".esttimeforoutof.esttimeforcarrier").hide();
			}
		}

	});
	$(document).on("change", ".hide_backorder", function () {
		if ($(this).val() == 1) {
			$(".esttimeforbackorder").hide();
		} else {
			$(".esttimeforbackorder").show();
			if ($('.enablecarrier:checked').val() == 0) {
				$(".esttimeforbackorder.esttimeforcarrier").hide();
			}
		}

	});

	$(document).on("click", ".rpesp_adddayrow", function () {
		$("#rpesp_tblinitrow").find(".rpesp_initrow").clone().insertAfter('#th_rpesp_specific_day');
	});

	$(document).on("click", ".rpesp_removedayrow", function () {
		$(this).parent().parent().remove();
	});

	$(document).on("click", ".rpesp_addperiodrow", function () {
		$("#rpesp_tblinitperiodrow").find(".rpesp_thinitperiodrow").clone().insertAfter('#th_rpesp_specific_period_day');
	});

	$(document).on("click", ".rpesp_removeperiodrow", function () {
		$(this).parent().parent().remove();
	});

	$(document).ready(function () {
		$('.txtcolor').wpColorPicker();

		$('#the-list').on('click', '.editinline', function () {
			if (typeof inlineEditPost != "undefined") {
				inlineEditPost.revert();
				var $post_id = $(this).closest('tr').attr('id');
				$post_id = $post_id.replace('post-', '');
				var $wcan_inline_data = $('#rpwoo_product_delivery_inline_' + $post_id);
				$('input[name="est_delivery_time"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttime').text());
				$('input[name="carrier_est_delivery_time"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_carrier_esttime').text());
				$('input[name="est_delivery_text"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttext').text());
				$('input[name="alt_est_delivery_text"]', '.inline-quick-edit').val($wcan_inline_data.find('._alt_delivery_esttext').text());
				$('input[name="est_order_delivery_text"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttext_order').text());
				$('input[name="est_delivery_time_outofstock"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttime_outofstock').text());
				$('input[name="est_delivery_text_outofstock"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttext_outofstock').text());
				$('input[name="est_delivery_time_backorder"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttime_backorder').text());
				$('input[name="est_delivery_text_backorder"]', '.inline-quick-edit').val($wcan_inline_data.find('._delivery_esttext_backorder').text());
				$('input[name="alt_est_delivery_text_backorder"]', '.inline-quick-edit').val($wcan_inline_data.find('._alt_delivery_esttext_backorder').text());
				if ($wcan_inline_data.find('._delivery_enable').html() == '1') {
					$('input[name="enable_delivery_date"]', '.inline-quick-edit').attr("checked", true);
				}
				if ($wcan_inline_data.find('._delivery_enable_for_variation').html() == '1') {
					$('input[name="enable_delivery_date_variation"]', '.inline-quick-edit').attr("checked", true);
				}
			}


		});
	});

})(jQuery);
var mediaUploader;
var objRpesp = {
	openUploader: function (eleID) {

		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			}, multiple: false
		});
		mediaUploader.on('select', function () {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery("#" + eleID).find('.hidden_imginput').val(attachment.id);
			jQuery("#" + eleID).find('.rpesp-upload-button').hide();
			jQuery("#" + eleID).find('.rccb_remove_button').show();
			console.log(attachment.sizes);
			jQuery("#" + eleID).find('.image').html('<img width="60px" height="60px" src="' + attachment.sizes.full.url + '" />');
			jQuery("#" + eleID).find('.rccb_remove_button').html('<input type="button" class="button-primary" onclick="objRpesp.removeImage(\'' + eleID + '\');" value="Remove" />');
		});
		mediaUploader.open();
	},
	removeImage: function (eleID) {
		jQuery("#" + eleID).find('.hidden_imginput').val('');
		jQuery("#" + eleID).find('.image').html('');
		jQuery("#" + eleID).find('.rpesp-upload-button').show();
		jQuery("#" + eleID).find('.rccb_remove_button').hide();
	}
};
