    function onResize() {
        if (jQuery(window).width() <= 640) {
            jQuery('.payMethodList').addClass('mobile');
        } else {
            jQuery('.payMethodList').removeClass('mobile');
        }
    }

    onResize();
    jQuery(window).resize(function () {
        onResize();
    });

    function setP24method(method) {
        method = parseInt(method);
        jQuery('input[name=p24_method]').val(method > 0 ? method : "");
    }

    function choosePaymentMethod() {
        var checkedPayment = false;

        var setP24channel = function (paymentIdRaw) {
            var paymentId = Number(paymentIdRaw);
            if (266 === paymentId) {
                jQuery('input[name=p24_channel]').val('2048');
            } else {
                jQuery('input[name=p24_channel]').val('');
            }
        };

        jQuery('.bank-box').click(function () {
            jQuery('.bank-box').removeClass('selected').addClass('inactive');
            jQuery(this).addClass('selected').removeClass('inactive');
            jQuery('.extra-promoted-box').removeClass('selected').addClass('inactive');
            if (jQuery(this).parents('.payMethodList').hasClass('checkoutView')) {
                var inputs = jQuery(this).parents('.checkoutView').find('input[type="checkbox"]');
                jQuery(inputs).removeAttr('checked');
                jQuery(inputs).prop('checked', false);

                var input = jQuery(this).find('input[type="checkbox"]');
                jQuery(input).attr('checked', 'checked');
                jQuery(input).prop('checked', true);
                checkedPayment = true;
                jQuery('#payment_method_przelewy24_payment').trigger('change');
            }
            setP24method(jQuery(this).attr('data-id'));
            setP24channel(jQuery(this).data('id'));
            setP24recurringId(jQuery(this).attr('data-cc'));
        });

        jQuery('.bank-item input').change(function () {
            setP24method(jQuery(this).closest('.bank-item').attr('data-id'));
            setP24recurringId(jQuery(this).attr('data-cc'));
        });

        jQuery('#payment_method_przelewy24_payment').trigger('change');

        jQuery('input[name=payment_method_id]:checked:first').closest('.input-box.bank-item').each(function () {
            setP24method(jQuery(this).attr('data-id'));
        });

        jQuery(".box-wrapper").click(function () {
            const $thisElement = jQuery(this);
            if($thisElement.hasClass('selected')){
                jQuery('input[name=p24_channel]').val('');
            }else{
                jQuery('.bank-box').removeClass('selected').addClass('inactive');
                jQuery("#p24-now-box").addClass('selected').removeClass('inactive');
                jQuery('input[name=p24_channel]').val('2048');
                setP24method(jQuery(this).data('id'));
                setP24channel(jQuery(this).data('id'));
                setP24recurringId('');
            }
        });

    }
    jQuery('<style>.moreStuff.translated:before{content:"' + p24_payment_php_vars.payments_msg4js + '"} </style>').appendTo('head');
    jQuery('.moreStuff').toggleClass('translated');

var sessionId = false;
var sign = false;
var payInShopScriptRequested = false;

function requestJsAjaxCard() {
    jQuery.ajax(jQuery('#p24_ajax_url').val(), {
        method: 'POST', type: 'POST',
        data: {
            action: 'trnRegister',
            p24_session_id: jQuery('[name=p24_session_id]').val(),
            order_id: jQuery('#p24_woo_order_id').val(),
            hashed_order_id: jQuery('#p24_woo_hashed_order_id').val()
        },
        error: function () {
            payInShopFailure();
        },
        success: function (response) {
            try {
                var data = JSON.parse(response);
                sessionId = data.sessionId;
                sign = data.p24_sign;
                jQuery('#P24FormArea').html("");
                jQuery("<div></div>")
                    .attr('id', 'P24FormContainer')
                    .attr('data-sign', sign)
                    .attr('data-successCallback', 'payInShopSuccess')
                    .attr('data-failureCallback', 'payInShopFailure')
                    .attr('data-client-id', data.client_id)
                    .attr('data-dictionary', jQuery('#p24_dictionary').val())
                    .appendTo('#P24FormArea')
                    .parent().slideDown()
                ;
                if (document.createStyleSheet) {
                    document.createStyleSheet(data.p24cssURL);
                } else {
                    jQuery('head').append('<link rel="stylesheet" type="text/css" href="' + data.p24cssURL + '" />');
                }
                if (!payInShopScriptRequested) {
                    jQuery.getScript(data.p24jsURL, function () {
                        P24_Transaction.init();
                        jQuery('#P24FormContainer');
                        payInShopScriptRequested = false;
                        window.setTimeout(function () {
                            jQuery('#P24FormContainer button').show().on('click', function () {
                                if (P24_Card.validate()) {
                                    jQuery(this).hide().after("<div class='loading' />");
                                }
                            });
                        }, 500);
                    });
                }
                payInShopScriptRequested = true;
            } catch (e) {
                window.location.reload();
            }
        }
    });
}

function showPayJsPopup() {
    $termsAcceptanceCheckbox = jQuery('[name=p24_regulation_accept][type=checkbox]');
    if (0 !== $termsAcceptanceCheckbox.length && !$termsAcceptanceCheckbox.prop('checked')) {
        jQuery('#place_order').click();

        return;
    }

    if (jQuery('#P24FormAreaHolder:visible').length == 0) {
        setP24method("");
        jQuery('#P24FormAreaHolder').appendTo('body');
        jQuery('#proceedPaymentLink').closest('a').fadeOut();

        jQuery('#P24FormAreaHolder').fadeIn();
        if (typeof P24_Transaction != 'object') {
            requestJsAjaxCard();
        }
    }
}

function hidePayJsPopup() {
    jQuery('#P24FormAreaHolder').fadeOut();
    jQuery('#proceedPaymentLink:not(:visible)').closest('a').fadeIn();
}

function payInShopSuccess(orderId, oneclickOrderId) {

        jQuery.ajax(jQuery('#p24_ajax_url').val(), {
            method: 'POST', type: 'POST',
            data: {
                action: 'rememberOrderId',
                sessionId: jQuery('[name=p24_session_id]').val(),
                orderId: orderId,
                oneclickOrderId: oneclickOrderId,
                sign: jQuery('[name=p24_sign]').val()
            }
        });
    window.setTimeout(function () {
        window.location = jQuery('[name=p24_url_return]').val();
    }, 1000);
}

    function setP24recurringId(id,name) {
        id = parseInt(id);
        if (name ==  undefined) name = jQuery('[data-cc='+id+'] .bank-name').text().trim() + ' - ' + jQuery('[data-cc='+id+'] .bank-logo span').text().trim();
        jQuery('input[name=p24_cc]').val( id > 0 ? id : "" );
        if (id > 0) setP24method(0);
    }

    function p24_processPayment() {
        console.log('processPayment');

        var ccid = parseInt(jQuery('input[name=p24_cc]').val());
        var fullCardData = jQuery('#p24-combined-cart-data').data('combined');
        var $additionalOrderData = jQuery('#p24-additional-order-data');
        var url = $additionalOrderData.data('ajax-url');


        var payByCard = function (data) {
            data['action'] = 'cardPay';
            data['orderId'] = $additionalOrderData.data('order-id');
            data['hashedOrderId'] = $additionalOrderData.data('hashed-order-id');
            data['p24sessionId'] = jQuery('input[name=p24_session_id]').val(),

            jQuery.ajax({
                url: url,
                method: 'POST', type: 'POST',
                data: data,
                success: function (resultJson) {
                    let result = jQuery.parseJSON(resultJson);
                    if (result.success && result.redirect && result.redirectUrl) {
                        setTimeout(function () {
                            window.location.href = result.redirectUrl;
                        }, 1000);
                    } else if (result.success) {
                        setTimeout(function () {
                            window.location.href = jQuery('[name=p24_url_return]').val();
                        }, 1000);
                    } else {
                        payInShopFailure();
                    }
                }
            });
        }

        if (fullCardData) {
            payByCard(fullCardData);
        } else if (isNaN(ccid) || ccid == 0) {
            var myform = document.getElementById("przelewy_payment_form");
            var fd = new FormData(myform );
            jQuery("#przelewy_payment_form :input").prop("disabled", true);
            jQuery.ajax({
                url: jQuery("#przelewy_payment_form input[name=p24_api_url]").val(),
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data) {
                    window.location.replace(data.url);
                },
                error: function () {
                    alert('internal error');
                }
            });
        } else if (ccid > 0) { // recuring
            var ra = jQuery('#przelewy_payment_form [name=p24_regulation_accept]').prop('checked') ? '1' : '';
            jQuery('#przelewy24FormRecuring [name=p24_regulation_accept]').val(ra);
            jQuery('#przelewy24FormRecuring').submit();
        }

        return false;
    }

    function removecc(ccid) {
        jQuery('form#cardrm input[name=cardrm]').val(ccid).closest('form').submit();
    }

    // payinshop

function payInShopFailure() {

    //wyświetlamy odpowiedź
    jQuery('#P24FormArea').html("<span class='info'>" + p24_payment_php_vars.error_msg4js + "</span>");  //'Wystąpił błąd. Spróbuj ponownie lub wybierz inną metodę płatności.'
    P24_Transaction = undefined;
    window.location = jQuery('[name=p24_url_return]').val();
}
    var selector = '#P24_registerCard';

    var waitForEl = function(selector, callback) {
        if (jQuery(selector).length) {
            jQuery('#P24_registerCard').prop('checked', (p24_payment_php_vars.forget_card == 1 ? false : true));
            if (!parseInt(p24_payment_php_vars.show_save_card)) {
                jQuery(jQuery(selector).parents('p')).hide();
            }
            jQuery(selector).on('change', function () {
                var payload = jQuery(this).prop('checked') ? 0 : 1;
                var url = jQuery('#p24-link-to-my-account').data('link');
                jQuery.ajax({
                    method: 'POST',
                    url: url,
                    data: {
                        act: 'cc_forget',
                        cc_forget: payload
                    }
                });
            })
        } else {
            setTimeout(function() {
                waitForEl(selector, callback);
            }, 100);
        }
    };

    waitForEl(selector, function() {
    });

var tryArmBlikBox = function() {
    var $additional_order_data = jQuery('#p24-additional-order-data')
    var $termsAcceptanceCheckbox = jQuery('[name="p24_regulation_accept"]');
    var url = jQuery('#p24_ajax_url').val();
    if (!url) {
        url = $additional_order_data.data('ajax-url');
    }

    var blikModalError = function() {
        var $modal = jQuery('#p24-blik-modal');
        $modal.removeClass('loading');
        $modal.find('.error').show();
    };

    var executePaymentByBlikCode = function(trnData, blikCode, onError) {
        jQuery.ajax({
            url: url,
            method: 'POST', type: 'POST',
            dataType: 'json',
            data: {
                'action': 'executePaymentByBlikCode',
                'token': trnData.token,
                'blikCode': blikCode
            }
        }).success(function (response) {
            if (response.success) {
                setTimeout(function () {
                    location = jQuery('#przelewy_payment_form input[name=p24_url_return]').val();
                }, 3000 /* Few seconds to accept transaction. */ );
            } else {
                onError();
            }
        }).error(onError);
    };

    var prepareModal = function ( onClose ) {
        var $modal = jQuery('#p24-blik-modal');
        if (!$modal.length) {
            return false;
        }
        $modal.addClass('loading');
        $modalBg = jQuery('#p24-blik-modal-background');
        if ( onClose ) {
            $modal.find('.close-modal').on('click', function (e) {
                e.preventDefault();
                onClose();
            });
        }
        $modalBg.show();
        return true;
    };

    var displayModal = function (nextSteep) {
        var $modal = jQuery('#p24-blik-modal');
        $modal.removeClass('loading');
        var $button = $modal.find('button');
        $button.on('click', function (e) {
            e.preventDefault();
            var $codeInput = $modal.find('input[type=text]');
            var code = $codeInput.val();
            var $acceptedInput = $modal.find('input[type=checkbox]');
            if ($acceptedInput.length && !$acceptedInput.prop('checked')) {
                $modal.find('.error-terms').show();
            } else if (/^\d{6}$/.test(code)) {
                $modal.addClass('loading');
                nextSteep(code);
            } else {
                $modal.find('.error-common').show();
            }
        });
    };

    var trnRegisterPromise = function () {
        var p24_session_id = jQuery('[name=p24_session_id]').val();
        var order_id = jQuery('#p24_woo_order_id').val();
        var hashed_order_id = jQuery('#p24_woo_hashed_order_id').val();
        if (!order_id) {
            order_id = $additional_order_data.data('order-id');
            hashed_order_id = $additional_order_data.data('hashed-order-id');
        }
        return jQuery.ajax({
            url: url,
            method: 'POST', type: 'POST',
            dataType: 'json',
            data: {
                action: 'trnRegister',
                p24_session_id: p24_session_id,
                order_id: order_id,
                hashed_order_id: hashed_order_id
            }
        });
    };

    var trnRegisterLong = function () {
        trnRegisterPromise().fail(payInShopFailure).done(function (data) {
            var nextSteep = function (code) {
                executePaymentByBlikCode(data, code, blikModalError);
            };
            displayModal(nextSteep)
        });
    };

    var trnRegisterIfTerms = function() {
        if ($termsAcceptanceCheckbox.attr('type') === 'checkbox' && !$termsAcceptanceCheckbox.is(':checked')) {
            jQuery('#place_order').click();
            return;
        }
        var hasModal = prepareModal(false);
        if (hasModal) {
            trnRegisterLong();
        }
    };

    var tryArmBlikBoxConfirmation = function () {
        /* Tile mode. */
        var $logoBox = jQuery('#p24-bank-grid a.bank-box[data-id=181]');
        if ($logoBox.length) {
            $logoBox.on('click', function (e) {
                e.preventDefault();
                trnRegisterIfTerms();
            });
        }
        /* Text mode. */
        var $ourRadioInput = jQuery('#przelewy_method_id_181-');
        if ($ourRadioInput.length) {
            $ourRadioInput.on('change', function (e) {
                if (!$ourRadioInput.prop('checked')) {
                    /* Nothing to do. */
                    return;
                }
                trnRegisterIfTerms();
            });
        }
        /* There is a lot of magic in WooCommerce JavaScript. */
        var $radioBox = jQuery('#payment_method_przelewy24_extra_181');
        if ($radioBox.length) {
            $radioBox.prop('checked', false);
            jQuery("body").on("change", ".wc_payment_methods input[name='payment_method']", function () {
                var $elm = jQuery(this);
                if ($elm.prop('checked') && $elm.val() === 'przelewy24_extra_181') {
                    var hasModal = jQuery('#p24-blik-modal').length;
                    if (hasModal) {
                        var $checkoutButton = jQuery('.woocommerce-checkout');
                        var $terms = jQuery('#terms');
                        if ($terms.length && !$terms.prop('checked')) {
                            $checkoutButton.submit();
                            $elm.prop('checked', false)
                        } else {
                            var nextSteep = function (code) {
                                jQuery('#p24-blik-code-input').val(code);
                                $checkoutButton.submit();
                            }
                            prepareModal(false);
                            displayModal(nextSteep);
                        }
                    }
                }
            });
        }
    };

    var tryExecuteBlik = function () {
        var $input = jQuery('#przelewy_payment_form input[name=p24_provided_blik_code]');
        if ($input.length) {
            var code = $input.val();
            if (code) {
                var onError = function () {
                    var href = jQuery('#przelewy_payment_form a.button.cancel').attr('href');
                    location = href;
                };
                var hasModal = prepareModal(onError);
                if (hasModal) {
                    if ($termsAcceptanceCheckbox.attr('type') === 'checkbox') {
                        /* We assume the the terms has been accepted on screen before. */
                        $termsAcceptanceCheckbox.prop('checked', true);
                    }
                    trnRegisterPromise().fail(onError).done(function (data) {
                        executePaymentByBlikCode(data, code, onError);
                    });
                }
            }
        }
    };

    tryArmBlikBoxConfirmation();
    tryExecuteBlik();
};

jQuery(function () {
    var $bgLayer = jQuery('#p24-dynamic-whole-background');
    if ($bgLayer.length) {
        jQuery('body').append($bgLayer);
        $bgLayer.css('display', 'block');
        jQuery('#przelewy_payment_form input[name=p24_regulation_accept]').prop("required", false);
        p24_processPayment();
    }
});

jQuery(document).ready(function () {
    choosePaymentMethod();

    jQuery('body').on('change', '[name="p24_regulation_accept"]:checked', function() {
        var $selectedCreditCardPayment = jQuery('.bank-box.bank-item.selected[onclick^="showPayJsPopup"]');
        if ($selectedCreditCardPayment.length) {
            $selectedCreditCardPayment.click();
        }
    });

    tryArmBlikBox();
});

/* Hide Apple Pay if not configured or not available. */
jQuery(document).ready(function () {
    //alert('ok, hit, p24');
    var onCheckout = function() {
        jQuery('li.payment_method_przelewy24_extra_232').css('display', 'none');
    };

    var hasApplePay = window.ApplePaySession && window.ApplePaySession.canMakePayments();
    if (!hasApplePay) {
        /* There are different places and modes the payment can be displayed. */
        onCheckout();
        jQuery(document.body).on('updated_checkout', onCheckout);
        jQuery('#p24-bank-grid a.bank-box.bank-item[data-id=232]').css('display', 'none');
        jQuery('#p24-bank-grid div.input-box.bank-item[data-id=232]').parent().css('display', 'none');
    }
});
