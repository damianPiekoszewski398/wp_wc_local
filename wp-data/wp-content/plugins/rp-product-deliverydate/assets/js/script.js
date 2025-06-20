(function ($) {
    "use strict";
    $(document).on('updated_shipping_method', function () {
        $("[name='update_cart']").removeAttr("disabled").trigger("click");
    });
    $(document).ready(function () {
        $(".single_variation_wrap").on("show_variation", function (event, variation) {
            setTimeout(function () {
                $(".variation_date").hide();
                $(".date_for_variation").hide();
                if ($(".date_variation_" + variation.variation_id).length > 0) {
                    $(".date_variation_" + variation.variation_id).css('display', 'flex');
                    $(".variation_date").show();
                }
            }, 500);
        });



        if ((typeof RPPDDF) != "undefined" && RPPDDF.enableAjax && RPPDDF.isWCPage) {
            if (RPPDDF.isProductPage) {
                rpLoadDeliveryDate();
                if (RPPDDF.enableCarrier) {
                    var productId = $(".rp_estimated_date_carrier_date").attr("data-pid");
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: RPPDDF.ajaxUrl + "?action=load_carrier_date",
                        data: {
                            'product_id': productId
                        },
                        success: function (data) {
                            if (data.success && data.carrier_text != "") {
                                $(".rp_estimated_date_carrier_date").replaceWith(data.carrier_text);
                            }
                        }
                    });
                }
            }
        }

    });
})(jQuery);
function rpLoadDeliveryDate() {
    var pid = [];
    jQuery(".rp_estimated_date").each(function () {
        if (jQuery(this).attr("data-pid") && jQuery(this).attr("data-loaded") && jQuery(this).attr("data-loaded") == "false") {
            pid.push(jQuery(this).attr("data-pid"));
        }

    });
    if (pid.length > 0) {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: RPPDDF.ajaxUrl + "?action=load_product_date_shop",
            data: {
                'ids': pid
            },
            success: function (data) {
                if (data.success && data.text && Object.keys(data.text).length > 0) {
                    jQuery.each(data.text, function (key, val) {
                        jQuery(".dpid_" + key).replaceWith(val);
                        jQuery(".dpid_" + key).attr("data-loaded", "true");
                    });
                    if (RPPDDF.isProductPage) {
                        if (jQuery(".variations_form").length > 0) {
                            var variationId = jQuery("input.variation_id").val();
                            if (parseInt(variationId) > 0) {
                                jQuery(".date_for_variation").hide();
                                jQuery(".date_variation_" + variationId).css('display', 'flex');
                                jQuery(".variation_date").show();
                            }
                        }
                    }
                }

            }
        });
    }
}
