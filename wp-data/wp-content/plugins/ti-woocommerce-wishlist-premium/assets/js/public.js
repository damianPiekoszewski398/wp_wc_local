"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
// Add to wishlist
(function ($) {
  tinvwl_add_to_wishlist.new_lists = {};

  //Add to wishlist main function.
  $.fn.tinvwl_to_wishlist = function (so) {
    var sd = {
      api_url: window.location.href.split('?')[0],
      text_create: window.tinvwl_add_to_wishlist['text_create'],
      text_already_in: window.tinvwl_add_to_wishlist['text_already_in'],
      class: {
        dialogbox: '.tinvwl_add_to_select_wishlist',
        select: '.tinvwl_wishlist',
        newtitle: '.tinvwl_new_input',
        dialogbutton: '.tinvwl_button_add'
      },
      redirectTimer: null,
      onPrepareList: function onPrepareList() {},
      onGetDialogBox: function onGetDialogBox() {},
      onPrepareDialogBox: function onPrepareDialogBox() {
        if (!$('body > .tinv-wishlist').length) {
          $('body').append($('<div>').addClass('tinv-wishlist'));
        }
        $(this).appendTo('body > .tinv-wishlist');
      },
      onCreateWishList: function onCreateWishList(wishlist) {
        if (tinvwl_add_to_wishlist.protected_list_types.includes(wishlist.type)) {
          return;
        }
        $(this).append($('<option>').html(wishlist.title ? wishlist.title : tinvwl_add_to_wishlist.text_default_title).val(wishlist.ID));
      },
      onSelectWishList: function onSelectWishList() {
        var el = $(this);
        $.each(tinvwl_add_to_wishlist.new_lists, function (key, value) {
          if (!(0 < el.find('option[value=\'' + key + '\']').length)) {
            el.find('option:last').before($('<option>').html(value).val(key));
          }
        });
        var options = el.find('option:not(:first):not(:last)');
        if (1 < options.length) {
          var arr = options.map(function (_, o) {
            return {
              t: $(o).text(),
              v: o.value
            };
          }).get();
          arr.sort(function (o1, o2) {
            return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
          });
          options.each(function (i, o) {
            o.value = arr[i].v;
            $(o).text(arr[i].t);
          });
        }
      },
      onDialogShow: function onDialogShow(element) {
        $(this).addClass('tinv-modal-open');
        FocusTrap('body > .tinv-wishlist');
        $(element).removeClass('ftinvwl-pulse');
      },
      onDialogHide: function onDialogHide(element) {
        $(this).removeClass('tinv-modal-open');
        $(element).removeClass('ftinvwl-pulse');
      },
      onInited: function onInited() {},
      onClick: function onClick() {
        if ($(this).is('.disabled-add-wishlist')) {
          return false;
        }
        if ($(this).is('.ftinvwl-animated')) {
          $(this).addClass('ftinvwl-pulse');
        }
        if (this.tinvwl_dialog) {
          this.tinvwl_dialog.show_list.call(this);
        } else {
          s.onActionProduct.call(this);
        }
      },
      onPrepareDataAction: function onPrepareDataAction(a, data) {
        $('body').trigger('tinvwl_wishlist_button_clicked', [a, data]);
      },
      filterProductAlreadyIn: function filterProductAlreadyIn(WList) {
        var WList = WList || [],
          data = {};
        $('form.cart[method=post], .woocommerce-variation-add-to-cart, form.vtajaxform[method=post]').find('input, select').each(function () {
          var name_elm = $(this).attr('name'),
            type_elm = $(this).attr('type'),
            value_elm = $(this).val();
          if ('checkbox' === type_elm || 'radio' === type_elm) {
            if ($(this).is(':checked')) {
              data['form' + name_elm] = value_elm;
            }
          } else {
            data['form' + name_elm] = value_elm;
          }
        });
        data = data['formvariation_id'];
        return WList.filter(function (wishlist) {
          if ('object' === _typeof(wishlist.in) && 'string' === typeof data) {
            var number = parseInt(data);
            return 0 <= wishlist.in.indexOf(number);
          }
          return wishlist.in;
        });
      },
      onMultiProductAlreadyIn: function onMultiProductAlreadyIn(WList) {
        var WList = WList || [];
        WList = s.onPrepareList.call(WList) || WList;
        WList = s.filterProductAlreadyIn.call(this, WList) || WList;
        $(this).parent().parent().find('.already-in').remove();
        var text = '';
        switch (WList.length) {
          case 0:
            break;
          default:
            var text = $('<ul>');
            $.each(WList, function (k, wishlist) {
              text.append($('<li>').html($('<a>').html(wishlist.title).attr({
                href: wishlist.url
              })).val(wishlist.ID));
            });
            break;
        }
        if (text.length) {
          $(this).closest('.tinv-modal-inner').find('img').after($('<div>').addClass('already-in').html(s.text_already_in + ' ').append(text));
        }
      },
      onAction: {
        redirect: function redirect(url) {
          if (s.redirectTimer) {
            clearTimeout(s.redirectTimer);
          }
          s.redirectTimer = window.setTimeout(function () {
            window.location.href = url;
          }, 4000);
        },
        force_redirect: function force_redirect(url) {
          window.location.href = url;
        },
        updatepage: function updatepage(is) {
          var selector = '.tinvwl_added_to_wishlist .tinv-close-modal, .tinvwl_added_to_wishlist .tinvwl_button_close, .tinvwl_added_to_wishlist .tinv-overlay';
          if (is) {
            $('body > .tinv-wishlist').on('click', selector, function (e) {
              window.location.reload();
            });
          } else {
            $('body > .tinv-wishlist').off('click', selector);
          }
        },
        wishlists: function wishlists(wishlist) {},
        msg: function msg(html) {
          if (!html) {
            return false;
          }
          var $msg = $(html).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          if (!s.redirectTimer) {
            s.removeTimer = window.setTimeout(function () {
              $msg.remove();
              if (s.redirectTimer) {
                clearTimeout(s.redirectTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
            if (s.redirectTimer) {
              clearTimeout(s.redirectTimer);
            }
            if (s.removeTimer) {
              clearTimeout(s.removeTimer);
            }
          });
        },
        status: function status(_status) {
          $('body').trigger('tinvwl_wishlist_added_status', [this, _status]);
        },
        removed: function removed(status) {},
        make_remove: function make_remove(status) {},
        wishlists_data: function wishlists_data(value) {
          set_hash(JSON.stringify(value));
        },
        wishlist_created: function wishlist_created(wishlist) {
          $.each(wishlist, function (key, value) {
            tinvwl_add_to_wishlist.new_lists[key] = value;
          });
        }
      }
    };
    sd.onActionProduct = function (id, name) {
      var data = {
          form: {},
          tinv_wishlist_id: id || '',
          tinv_wishlist_name: name || '',
          product_type: $(this).attr('data-tinv-wl-producttype'),
          product_id: $(this).attr('data-tinv-wl-product') || 0,
          product_variation: $(this).attr('data-tinv-wl-productvariation') || 0,
          product_action: $(this).attr('data-tinv-wl-action') || 'addto',
          redirect: window.location.href
        },
        a = this,
        formEl = [],
        hiddenFields = [],
        formData = new FormData();
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $('form.cart[method=post][data-product_id="' + $(this).attr('data-tinv-wl-product') + '"], form.vtajaxform[method=post][data-product_id="' + $(this).attr('data-tinv-wl-product') + '"]').each(function () {
        formEl.push($(this));
      });
      if (!formEl.length) {
        $(a).closest('form.cart[method=post], form.vtajaxform[method=post]').each(function () {
          formEl.push($(this));
        });
        if (!formEl.length) {
          formEl.push($('form.cart[method=post]'));
        }
      }
      $('.tinv-wraper[data-tinvwl_product_id="' + $(this).attr('data-tinv-wl-product') + '"]').each(function () {
        formEl.push($(this));
      });
      $.each(formEl, function (index, element) {
        $(element).find('input:not(:disabled), select:not(:disabled), textarea:not(:disabled)').each(function () {
          var name_elm = $(this).attr('name'),
            type_elm = $(this).attr('type'),
            value_elm = $(this).val(),
            count = 10,
            ti_merge_value = function ti_merge_value(o1, o2) {
              if ('object' === _typeof(o2)) {
                if ('undefined' === typeof o1) {
                  o1 = {};
                }
                for (var i in o2) {
                  if ('' === i) {
                    var j = -1;
                    for (j in o1) {
                      j = j;
                    }
                    j = parseInt(j) + 1;
                    o1[j] = ti_merge_value(o1[i], o2[i]);
                  } else {
                    o1[i] = ti_merge_value(o1[i], o2[i]);
                  }
                }
                return o1;
              } else {
                return o2;
              }
            };
          if ('button' === type_elm || 'undefined' == typeof name_elm) {
            return;
          }
          while (/^(.+)\[([^\[\]]*?)\]$/.test(name_elm) && 0 < count) {
            var n_name = name_elm.match(/^(.+)\[([^\[\]]*?)\]$/);
            if (3 === n_name.length) {
              var _value_elm = {};
              _value_elm[n_name[2]] = value_elm;
              value_elm = _value_elm;
            }
            name_elm = n_name[1];
            count--;
          }
          if ('file' === type_elm) {
            var file_data = $(this)[0].files;
            if (file_data) {
              formData.append(name_elm, file_data[0]);
            }
          }
          if ('checkbox' === type_elm || 'radio' === type_elm) {
            if ($(this).is(':checked')) {
              if (!value_elm.length && 'object' !== _typeof(value_elm)) {
                value_elm = true;
              }
              data.form[name_elm] = ti_merge_value(data.form[name_elm], value_elm);
            }
          } else {
            data.form[name_elm] = ti_merge_value(data.form[name_elm], value_elm);
          }
          if ('hidden' === type_elm) {
            hiddenFields.push(name_elm);
          }
        });
      });
      data.form['tinvwl-hidden-fields'] = hiddenFields;
      data = s.onPrepareDataAction.call(a, a, data) || data;
      $.each(data, function (key, value) {
        if ('form' === key) {
          $.each(value, function (k, v) {
            if ('object' === _typeof(v)) {
              v = JSON.stringify(v);
            }
            formData.append(key + '[' + k + ']', v);
          });
        } else {
          formData.append(key, value);
        }
      });
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        formData.append('multi_wishlists', tinvwl_add_to_wishlist.multi_wishlists);
      }
      $.ajax({
        url: s.api_url,
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData
      }).done(function (body) {
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, body]);
        s.onDialogHide.call(a.tinvwl_dialog, a);
        if ('object' === _typeof(body)) {
          for (var k in body) {
            if ('function' === typeof s.onAction[k]) {
              s.onAction[k].call(a, body[k]);
            }
          }
        } else {
          if ('function' === typeof s.onAction.msg) {
            s.onAction.msg.call(a, body);
          }
        }
      });
    };
    var s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      if (!$(this).attr('data-tinv-wl-list')) {
        return false;
      }
      if (s.dialogbox) {
        if (s.dialogbox.length) {
          this.tinvwl_dialog = s.dialogbox;
        }
      }
      if (!this.tinvwl_dialog) {
        this.tinvwl_dialog = s.onGetDialogBox.call(this);
      }
      if (!this.tinvwl_dialog) {
        var _tinvwl_dialog = $(this).nextAll(s.class.dialogbox).eq(0);
        if (_tinvwl_dialog.length) {
          this.tinvwl_dialog = _tinvwl_dialog;
        }
      }
      if (this.tinvwl_dialog) {
        s.onPrepareDialogBox.call(this.tinvwl_dialog);
        if ('function' !== typeof this.tinvwl_dialog.update_list) {
          this.tinvwl_dialog.update_list = function (WL) {
            var $select = $(this).find(s.class.select).eq(0);
            $(this).find(s.class.newtitle).hide().val('');
            $select.html('');
            $.each(WL, function (k, v) {
              s.onCreateWishList.call($select, v);
            });
            if (s.text_create) {
              s.onCreateWishList.call($select, {
                ID: '',
                title: s.text_create,
                in: false
              });
            }
            s.onMultiProductAlreadyIn.call($select, WL);
            s.onSelectWishList.call($select, WL);
            $(this).find(s.class.newtitle).toggle('' === $select.val());
          };
        }
        if ('function' !== typeof this.tinvwl_dialog.show_list) {
          this.tinvwl_dialog.show_list = function () {
            var WList = JSON.parse($(this).attr('data-tinv-wl-list')) || [];
            if (WList.length) {
              WList = s.onPrepareList.call(WList) || WList;
              this.tinvwl_dialog.update_list(WList);
              s.onDialogShow.call(this.tinvwl_dialog, this);
            } else {
              s.onActionProduct.call(this);
            }
          };
        }
        var a = this;
        $(this.tinvwl_dialog).find(s.class.select).on('change', function () {
          $(a.tinvwl_dialog).find(s.class.newtitle).toggle('' === $(this).val());
        });
        $(this.tinvwl_dialog).find(s.class.dialogbutton).off('click').on('click', function () {
          var b = $(a.tinvwl_dialog).find(s.class.select),
            c = $(a.tinvwl_dialog).find(s.class.newtitle),
            d;
          if (b.val() || c.val()) {
            s.onActionProduct.call(a, b.val(), c.val());
          } else {
            d = c.is(':visible') ? c : b;
            d.addClass('empty-name-wishlist');
            window.setTimeout(function () {
              d.removeClass('empty-name-wishlist');
            }, 1000);
          }
        });
      }
      $(this).off('click').on('click', s.onClick);
      s.onInited.call(this, s);
    });
  };
  $(document).ready(function () {
    // Add to wishlist button click
    $('body').on('click keydown', '.tinvwl_add_to_wishlist_button', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
        e.preventDefault();
      }
      $('body').trigger('tinvwl_add_to_wishlist_button_click', [this]);
      if ($(this).is('.disabled-add-wishlist')) {
        e.preventDefault();
        window.alert(tinvwl_add_to_wishlist.i18n_make_a_selection_text);
        return;
      }
      if ($(this).is('.inited-add-wishlist')) {
        return;
      }
      $(this).tinvwl_to_wishlist({
        onInited: function onInited(s) {
          $(this).addClass('inited-add-wishlist');
          s.onClick.call(this);
        }
      });
    });
    $('body').on('click', '.tinvwl_cart_to_wishlist_button', function (e) {
      $(this).tinvwl_to_wishlist({
        onInited: function onInited(s) {
          $(this).addClass('inited-add-wishlist');
          s.onClick.call(this);
        },
        onPrepareDataAction: function onPrepareDataAction(a, data) {
          data.tinv_product_key = $(this).attr('data-tinv-wl-cart-key');
          data.cart_item_to_wishlist_id = data.tinv_wishlist_id;
          delete data.tinv_wishlist_id;
          return data;
        },
        onAction: {
          updatepage: function updatepage(is) {
            var selector = '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay';
            if (is) {
              $(selector).on('click', function (e) {
                window.location.reload();
              });
            }
          }
        }
      });
    });
    $('body').on('click', '.tinvwl_all_cart_to_wishlist_button', function (e) {
      $(this).tinvwl_to_wishlist({
        onInited: function onInited(s) {
          $(this).addClass('inited-add-wishlist');
          s.onClick.call(this);
        },
        onPrepareDataAction: function onPrepareDataAction(a, data) {
          data.cart_to_wishlist_id = data.tinv_wishlist_id;
          delete data.tinv_wishlist_id;
          return data;
        },
        onAction: {
          updatepage: function updatepage(is) {
            var selector = '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay';
            if (is) {
              $(selector).on('click', function (e) {
                window.location.reload();
              });
            }
          }
        }
      });
    });

    //Remove button ajax
    $('body').on('click keydown', 'button[name="tinvwl-remove"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var el = $(this);
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var data = {
        'tinvwl-product_id': el.val(),
        'tinvwl-action': 'remove',
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.data('tinvwl_paged') || el.closest('form').data('tinvwl_paged'),
        'tinvwl-per-page': el.data('tinvwl_per_page') || el.closest('form').data('tinvwl_per_page'),
        'tinvwl-sharekey': el.data('tinvwl_sharekey') || el.closest('form').data('tinvwl_sharekey')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.status) {
          $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
          $('.tinvwl-break-input').tinvwl_break_submit({
            selector: '.tinvwl-break-input-filed'
          });
          $('.tinvwl-break-checkbox').tinvwl_break_submit({
            selector: 'table td input[type=checkbox]',
            validate: function validate() {
              return $(this).is(':checked');
            }
          });
          jQuery.fn.tinvwl_get_wishlist_data();
        }
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    //Add to cart button ajax
    $('body').on('click keydown', 'button[name="tinvwl-add-to-cart"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var el = $(this);
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var data = {
        'tinvwl-product_id': el.val(),
        'tinvwl-qty': {},
        'tinvwl-action': 'add_to_cart_single',
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.data('tinvwl_paged') || el.closest('form').data('tinvwl_paged'),
        'tinvwl-per-page': el.data('tinvwl_per_page') || el.closest('form').data('tinvwl_per_page'),
        'tinvwl-sharekey': el.data('tinvwl_sharekey') || el.closest('form').data('tinvwl_sharekey')
      };
      data['tinvwl-qty'][el.val()] = el.closest('form').find('input[name="wishlist_qty[' + el.val() + ']"]').val();
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        $(document.body).trigger('wc_fragment_refresh');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    //Move to wishlist button ajax
    function move_dialog(e) {
      e.tinvwl_to_wishlist({
        class: {
          dialogbox: '.tinvwl_wishlist_move',
          select: '.tinvwl_wishlist',
          newtitle: '.tinvwl_new_input',
          dialogbutton: '.tinvwl_button_move'
        },
        onPrepareList: function onPrepareList() {
          return this.filter(function (a) {
            return !a.hide;
          });
        },
        onDialogShow: function onDialogShow(el) {
          var modal = $(this);
          modal.addClass('tinv-modal-open');
          FocusTrap('body > .tinv-wishlist');
          modal.find('.tinvwl_button_move').off('click').on('click keydown', function (e) {
            if ('keydown' === e.type) {
              var keyD = e.key !== undefined ? e.key : e.keyCode;

              // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
              // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

              if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
                return;
              }
            }
            e.preventDefault();
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
              message: null,
              overlayCSS: {
                background: '#fff',
                opacity: 0.6
              }
            });
            var data = {
              'tinvwl-action': 'move_single',
              'tinvwl-security': tinvwl_add_to_wishlist.nonce,
              'tinvwl-paged': $(el).closest('form').data('tinvwl_paged'),
              'tinvwl-per-page': $(el).closest('form').data('tinvwl_per_page'),
              'tinvwl-sharekey': $(el).closest('form').data('tinvwl_sharekey'),
              'tinvwl-product-wid': $(el).attr('data-tinv-wl-product-wid'),
              'tinvwl-to-wl': modal.find('select.tinvwl_wishlist').val(),
              'tinvwl-new-wl': modal.find('input.tinvwl_new_input').val()
            };
            if (tinvwl_add_to_wishlist.wpml) {
              data.lang = tinvwl_add_to_wishlist.wpml;
            }
            if (tinvwl_add_to_wishlist.wpml_default) {
              data.lang_default = tinvwl_add_to_wishlist.wpml_default;
            }
            if (tinvwl_add_to_wishlist.stats) {
              data.stats = tinvwl_add_to_wishlist.stats;
            }
            if (tinvwl_add_to_wishlist.multi_wishlists) {
              data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
            }
            $.ajax({
              url: tinvwl_add_to_wishlist.wc_ajax_url,
              method: 'POST',
              cache: false,
              data: data,
              beforeSend: function beforeSend(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
                modal.remove();
              }
            }).done(function (response) {
              $(el).removeClass('inited-wishlist-action');
              $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
              if (response.redirect) {
                window.location.href = response.redirect;
              }
              if (response.msg) {
                var $msg = $(response.msg).eq(0);
                if (!$('body > .tinv-wishlist').length) {
                  $('body').append($('<div>').addClass('tinv-wishlist'));
                }
                $('body > .tinv-wishlist').append($msg);
                FocusTrap('body > .tinv-wishlist');
                $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
                  e.preventDefault();
                  $msg.remove();
                });
                var closeTimer;
                if (!closeTimer) {
                  closeTimer = window.setTimeout(function () {
                    $msg.remove();
                    if (closeTimer) {
                      clearTimeout(closeTimer);
                    }
                  }, tinvwl_add_to_wishlist.popup_timer);
                }
              }
              if (response.redirect) {
                return;
              }
              $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
              jQuery.fn.tinvwl_get_wishlist_data();
              if (response.wishlists_data) {
                set_hash(JSON.stringify(response.wishlists_data));
              }
              $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
            });
          });
        }
      });
    }
    $('.tinvwl_move_product_button').each(function () {
      move_dialog($(this));
    });
    $(document).on('tinvwl_wishlist_ajax_response', 'body', function () {
      $('.tinvwl_move_product_button').each(function () {
        move_dialog($(this));
      });
      $('.tinv-modal-btn').tinvwl_modal();
      $('.tinv_wishlist_create').tinvwl_validate_createwishlist();
      $('button[name="tinvwl-wishlist-remove-btn"]').tinvwl_psform();

      // Sorting products
      if ($.isFunction($.fn.sortable)) {
        $('.tinvwl-table-manage-list tbody').sortable({
          items: 'tr',
          cursor: 'move',
          axis: 'y',
          handle: 'td.product-sort',
          scrollSensitivity: 40
        });
      }
    });

    // Delete wishlist
    $.fn.tinvwl_psform = function (so) {
      var sd = {
          container: '.ti-ps-form'
        },
        s = $.extend(true, {}, sd, so);
      return $(this).each(function () {
        $(this).on('click', function (e) {
          if ('keydown' === e.type) {
            var keyD = e.key !== undefined ? e.key : e.keyCode;

            // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
            // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

            if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
              return;
            }
          }
          e.preventDefault();
          $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
            message: null,
            overlayCSS: {
              background: '#fff',
              opacity: 0.6
            }
          });
          var modal = $(this).closest('.tinv-modal');
          var data = {
            'tinvwl-manage-action': 'remove',
            'tinvwl-security': tinvwl_add_to_wishlist.nonce,
            'tinvwl-paged': $('#tinvwl-manage-wishlists').data('tinvwl_paged'),
            'tinvwl-wishlist-id': $(this).closest(s.container).find('input[name="tinvwl-wishlist-remove"]').val()
          };
          if (tinvwl_add_to_wishlist.wpml) {
            data.lang = tinvwl_add_to_wishlist.wpml;
          }
          if (tinvwl_add_to_wishlist.wpml_default) {
            data.lang_default = tinvwl_add_to_wishlist.wpml_default;
          }
          if (tinvwl_add_to_wishlist.stats) {
            data.stats = tinvwl_add_to_wishlist.stats;
          }
          if (tinvwl_add_to_wishlist.multi_wishlists) {
            data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
          }
          $.ajax({
            url: tinvwl_add_to_wishlist.wc_ajax_url,
            method: 'POST',
            cache: false,
            data: data,
            beforeSend: function beforeSend(xhr) {
              xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
              modal.removeClass('tinv-modal-open');
            }
          }).done(function (response) {
            if (response.redirect) {
              window.location.href = response.redirect;
            }
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
            if (response.msg) {
              var $msg = $(response.msg).eq(0);
              if (!$('body > .tinv-wishlist').length) {
                $('body').append($('<div>').addClass('tinv-wishlist'));
              }
              $('body > .tinv-wishlist').append($msg);
              FocusTrap('body > .tinv-wishlist');
              $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
                e.preventDefault();
                $msg.remove();
              });
              var closeTimer;
              if (!closeTimer) {
                closeTimer = window.setTimeout(function () {
                  $msg.remove();
                  if (closeTimer) {
                    clearTimeout(closeTimer);
                  }
                }, tinvwl_add_to_wishlist.popup_timer);
              }
            }
            if (response.redirect) {
              return;
            }
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
            jQuery.fn.tinvwl_get_wishlist_data();
            $(document.body).trigger('wc_fragment_refresh');
            if (response.wishlists_data) {
              set_hash(JSON.stringify(response.wishlists_data));
            }
            $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
          });
        });
      });
    };
    $('button[name="tinvwl-wishlist-remove-btn"]').tinvwl_psform();

    //Notifications update
    $('body').on('click keydown', 'button[name="tinvwl-action-notifications"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var form = $(this).closest('form'),
        formdata = {
          action: 'tinwl_notifications_update'
        };
      form.find('input, select, button').each(function () {
        switch ($(this).attr('type')) {
          case 'radio':
          case 'checkbox':
            if ($(this).is(':checked')) {
              formdata[$(this).attr('name')] = $(this).val().trim();
            }
            break;
          default:
            formdata[$(this).attr('name')] = $(this).val().trim();
        }
      });
      form.find('input[type=text]').val('');
      form.parents('.tinv_wishlist_notifications_modal').removeClass('tinv-modal-open');
      $.post(window.location.href, formdata, function (data) {
        $('.tinv-wishlist.woocommerce').eq(0).append(data);
      }, 'html');
    });

    //Ask for estimate
    $('body').on('click keydown', 'button[name="tinvwl_estimate"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var form = $(this).closest('form'),
        formdata = {
          action: 'tinwl_estimate'
        };
      form.find('input, select, button, textarea').each(function () {
        switch ($(this).attr('type')) {
          case 'radio':
          case 'checkbox':
            if ($(this).is(':checked')) {
              formdata[$(this).attr('name')] = $(this).val().trim();
            }
            break;
          default:
            formdata[$(this).attr('name')] = $(this).val().trim();
        }
      });
      form.find('input[type=text]').val('');
      form.parents('.estimate-dialogbox').removeClass('tinv-modal-open');
      $.post(window.location.href, formdata, function (data) {
        $('.tinv-wishlist.woocommerce').eq(0).append(data);
      }, 'html');
    });

    //Add all to cart button ajax
    $('body').on('click keydown', 'button[name="tinvwl-action-product_all"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var el = $(this);
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var data = {
        'tinvwl-action': 'add_to_cart_all',
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.closest('form').data('tinvwl_paged'),
        'tinvwl-per-page': el.closest('form').data('tinvwl_per_page'),
        'tinvwl-sharekey': el.closest('form').data('tinvwl_sharekey')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        $(document.body).trigger('wc_fragment_refresh');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    //Update wishlist
    $('body').on('click keydown', 'button[name="tinvwl-action-product_update"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var el = $(this);
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var products = [],
        quantities = {};
      $('tr[data-tinvwl-pid]').each(function () {
        products.push($(this).data('tinvwl-pid'));
        quantities[$(this).data('tinvwl-pid')] = $(this).closest('form').find('input[name="wishlist_qty[' + $(this).data('tinvwl-pid') + ']"]').val();
      });
      var data = {
        'tinvwl-products': products,
        'tinvwl-qty': quantities,
        'tinvwl-action': 'update',
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.closest('form').data('tinvwl_paged'),
        'tinvwl-per-page': el.closest('form').data('tinvwl_per_page'),
        'tinvwl-sharekey': el.closest('form').data('tinvwl_sharekey')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      if (tinvwl_add_to_wishlist.sort && $.isFunction($.fn.sortable)) {
        data['tinvwl-order'] = $('.tinvwl-table-manage-list tbody').sortable('toArray', {
          attribute: 'data-tinvwl-pid'
        });
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        $(document.body).trigger('wc_fragment_refresh');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    // Save Wishlists
    $('body').on('click keydown', 'button[name="tinvwl-action-manage_save"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var el = $(this);
      if (el.is('.inited-wishlist-action')) {
        return;
      }

      // Check for updates
      var updates = false;
      $('.tinvwl-name-to-rename a').each(function () {
        if ($(this).html() === $(this).closest('td').find('.tinvwl-rename-input input').val()) {
          $(this).closest('td').find('.tinvwl-rename-input').hide();
          $(this).closest('td').find('.tinvwl-name-to-rename').show();
          $(this).closest('td').find('.tinvwl-rename-button').show();
        } else {
          updates = true;
        }
      });
      $('select[name^="wishlist_privacy"]').each(function () {
        if ($(this).val() !== $(this).attr('data-tinvwl-current-privacy')) {
          updates = true;
        }
      });
      if (!updates) {
        window.alert(tinvwl_add_to_wishlist.i18n_update_settings_before);
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var wishlists = [],
        titles = {},
        privacies = {};
      $('input[name="wishlist_pr[]"]').each(function () {
        wishlists.push(this.value);
        titles[this.value] = $(this).closest('form').find('input[name="wishlist_name[' + this.value + ']"]').val();
        privacies[this.value] = $(this).closest('form').find('select[name="wishlist_privacy[' + this.value + ']"]').val();
      });
      var data = {
        'tinvwl-wishlists': wishlists,
        'tinvwl-wishlists-titles': titles,
        'tinvwl-wishlists-privacies': privacies,
        'tinvwl-manage-action': 'save',
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.closest('form').data('tinvwl_paged')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    //Bulk wishlist products action button ajax
    $('body').on('click keydown', 'button[name="tinvwl-action-product_apply"], button[name="tinvwl-action-product_selected"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var products = [],
        quantities = {};
      $('input[name="wishlist_pr[]"]:checked').each(function () {
        products.push(this.value);
        quantities[this.value] = $(this).closest('form').find('input[name="wishlist_qty[' + this.value + ']"]').val();
      });
      var el = $(this);
      if (!products.length || 'tinvwl-action-product_selected' !== el.attr('name') && !$('select#tinvwl_product_actions option').filter(':selected').val()) {
        alert(window.tinvwl_add_to_wishlist['tinvwl_break_submit']);
        return;
      }
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var action = '';
      if ('tinvwl-action-product_selected' === el.attr('name')) {
        action = 'add_to_cart_selected';
      } else {
        action = $('select#tinvwl_product_actions option').filter(':selected').val();
      }
      var data = {
        'tinvwl-products': products,
        'tinvwl-qty': quantities,
        'tinvwl-action': action,
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.closest('form').data('tinvwl_paged'),
        'tinvwl-per-page': el.closest('form').data('tinvwl_per_page'),
        'tinvwl-sharekey': el.closest('form').data('tinvwl_sharekey')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        if ('add_to_cart_selected' === action) {
          $(document.body).trigger('wc_fragment_refresh');
        }
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    //Bulk manage wishlists action button ajax
    $('body').on('click keydown', 'button[name="tinvwl-action-manage_apply"]', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode;

        // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }
      }
      e.preventDefault();
      var wishlists = [],
        titles = {},
        privacies = {},
        action = $('select#tinvwl_manage_actions option').filter(':selected').val();
      $('input[name="wishlist_pr[]"]:checked').each(function () {
        wishlists.push(this.value);
        titles[this.value] = $(this).closest('form').find('input[name="wishlist_name[' + this.value + ']"]').val();
        privacies[this.value] = $(this).closest('form').find('select[name="wishlist_privacy[' + this.value + ']"]').val();
      });
      var el = $(this);
      if (!wishlists.length || !$('select#tinvwl_manage_actions option').filter(':selected').val()) {
        alert(window.tinvwl_add_to_wishlist['tinvwl_break_submit']);
        return;
      }
      if (el.is('.inited-wishlist-action')) {
        return;
      }
      el.addClass('inited-wishlist-action');
      $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
      var data = {
        'tinvwl-wishlists': wishlists,
        'tinvwl-wishlists-titles': titles,
        'tinvwl-wishlists-privacies': privacies,
        'tinvwl-manage-action': action,
        'tinvwl-security': tinvwl_add_to_wishlist.nonce,
        'tinvwl-paged': el.closest('form').data('tinvwl_paged')
      };
      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }
      if (tinvwl_add_to_wishlist.wpml_default) {
        data.lang_default = tinvwl_add_to_wishlist.wpml_default;
      }
      if (tinvwl_add_to_wishlist.stats) {
        data.stats = tinvwl_add_to_wishlist.stats;
      }
      if (tinvwl_add_to_wishlist.multi_wishlists) {
        data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
      }
      $.ajax({
        url: tinvwl_add_to_wishlist.wc_ajax_url,
        method: 'POST',
        cache: false,
        data: data,
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
        }
      }).done(function (response) {
        el.removeClass('inited-wishlist-action');
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
        if (response.redirect) {
          window.location.href = response.redirect;
        }
        if (response.msg) {
          var $msg = $(response.msg).eq(0);
          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }
          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();
          });
          var closeTimer;
          if (!closeTimer) {
            closeTimer = window.setTimeout(function () {
              $msg.remove();
              if (closeTimer) {
                clearTimeout(closeTimer);
              }
            }, tinvwl_add_to_wishlist.popup_timer);
          }
        }
        if (response.redirect) {
          return;
        }
        if ('remove' === action) {
          $(document.body).trigger('wc_fragment_refresh');
        }
        $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
        jQuery.fn.tinvwl_get_wishlist_data();
        if (response.wishlists_data) {
          set_hash(JSON.stringify(response.wishlists_data));
        }
        $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
      });
    });

    // Disable add to wishlist button if variations not selected
    $(document).on('hide_variation', '.variations_form', function (a) {
      var e = $('.tinvwl_add_to_wishlist_button:not(.tinvwl-loop)[data-tinv-wl-product="' + $(this).data('product_id') + '"]');
      e.attr('data-tinv-wl-productvariation', 0);
      var originalBlockAjaxWishlistsData = tinvwl_add_to_wishlist.block_ajax_wishlists_data;
      tinvwl_add_to_wishlist.block_ajax_wishlists_data = true;
      $.fn.tinvwl_get_wishlist_data();
      tinvwl_add_to_wishlist.block_ajax_wishlists_data = originalBlockAjaxWishlistsData;
      if (e.length && !tinvwl_add_to_wishlist.allow_parent_variable) {
        a.preventDefault();
        e.addClass('disabled-add-wishlist');
      }
    });
    $(document).on('show_variation', '.variations_form', function (a, b, d) {
      var e = $('.tinvwl_add_to_wishlist_button:not(.tinvwl-loop)[data-tinv-wl-product="' + $(this).data('product_id') + '"]');
      e.attr('data-tinv-wl-productvariation', b.variation_id);
      var originalBlockAjaxWishlistsData = tinvwl_add_to_wishlist.block_ajax_wishlists_data;
      tinvwl_add_to_wishlist.block_ajax_wishlists_data = true;
      $.fn.tinvwl_get_wishlist_data();
      tinvwl_add_to_wishlist.block_ajax_wishlists_data = originalBlockAjaxWishlistsData;
      a.preventDefault();
      e.removeClass('disabled-add-wishlist');
    });

    // Refresh when storage changes in another tab
    $(window).on('storage onstorage', function (e) {
      if (hash_key === e.originalEvent.key && localStorage.getItem(hash_key) !== sessionStorage.getItem(hash_key)) {
        if (localStorage.getItem(hash_key)) {
          var data = JSON.parse(localStorage.getItem(hash_key));
          if ('object' === _typeof(data) && null !== data && (data.hasOwnProperty('products') || data.hasOwnProperty('counter'))) {
            set_hash(localStorage.getItem(hash_key));
          }
        }
      }
    });

    // Get wishlist data from REST API.
    var tinvwl_products = [],
      tinvwl_counter = false;
    $('a.tinvwl_add_to_wishlist_button').each(function () {
      if ('undefined' !== $(this).data('tinv-wl-product') && $(this).data('tinv-wl-product')) {
        tinvwl_products.push($(this).data('tinv-wl-product'));
      }
    });
    $('.wishlist_products_counter_number').each(function () {
      tinvwl_counter = true;
    });
    var get_data_ajax = function get_data_ajax(refresh) {
      if ((tinvwl_products.length || tinvwl_counter) && tinvwl_add_to_wishlist.user_interacted) {
        var data = {
          'tinvwl-action': 'get_data',
          'tinvwl-security': tinvwl_add_to_wishlist.nonce
        };
        if ('refresh' === refresh) {
          var form = $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear form[data-tinvwl_sharekey]');
          if (form.length) {
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').block({
              message: null,
              overlayCSS: {
                background: '#fff',
                opacity: 0.6
              }
            });
            data['tinvwl-paged'] = form.data('tinvwl_paged');
            data['tinvwl-per-page'] = form.data('tinvwl_per_page');
            data['tinvwl-sharekey'] = form.data('tinvwl_sharekey');
          }
        }
        if (tinvwl_add_to_wishlist.wpml) {
          data.lang = tinvwl_add_to_wishlist.wpml;
        }
        if (tinvwl_add_to_wishlist.wpml_default) {
          data.lang_default = tinvwl_add_to_wishlist.wpml_default;
        }
        if (tinvwl_add_to_wishlist.multi_wishlists) {
          data.multi_wishlists = tinvwl_add_to_wishlist.multi_wishlists;
        }
        if (tinvwl_add_to_wishlist.stats) {
          data.stats = tinvwl_add_to_wishlist.stats;
        }
        $.ajax({
          url: tinvwl_add_to_wishlist.wc_ajax_url,
          method: 'POST',
          cache: false,
          data: data,
          beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
          }
        }).done(function (response) {
          if ('refresh' === refresh) {
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').unblock();
            $(document.body).trigger('wc_fragment_refresh');
            $('div.tinv-wishlist.woocommerce.tinv-wishlist-clear').replaceWith(response.content);
            localStorage.setItem(hash_key + '_refresh', '');
          }
          if (response.wishlists_data) {
            set_hash(JSON.stringify(response.wishlists_data));
          }
          $('body').trigger('tinvwl_wishlist_ajax_response', [this, response]);
        });
      }
    };
    $.fn.tinvwl_get_wishlist_data = function (refresh) {
      if ('refresh' === refresh) {
        get_data_ajax(refresh);
        return;
      }
      if ($supports_html5_storage) {
        if (Cookies.get('tinvwl_update_data') !== undefined) {
          Cookies.set('tinvwl_update_data', 0, {
            expires: -1
          });
          localStorage.setItem(hash_key, '');
        }
        if (localStorage.getItem(hash_key)) {
          var data = JSON.parse(localStorage.getItem(hash_key));
          if ('object' === _typeof(data) && null !== data && (data.hasOwnProperty('products') || data.hasOwnProperty('counter'))) {
            if (!data.hasOwnProperty('lang') && !tinvwl_add_to_wishlist.wpml || tinvwl_add_to_wishlist.wpml && data.lang === tinvwl_add_to_wishlist.wpml) {
              if (Cookies.get('tinvwl_wishlists_data_counter') === undefined) {
                mark_products(data);
                return;
              }
              if (Cookies.get('tinvwl_wishlists_data_counter') == data.counter && (!data.hasOwnProperty('stats_count') || Cookies.get('tinvwl_wishlists_data_stats') == data.stats_count)) {
                mark_products(data);
                return;
              }
            }
          }
        }
      }
      if (tinvwl_add_to_wishlist.block_ajax_wishlists_data) {
        setTimeout(function () {
          mark_products(data);
        }, 500);
        return;
      }
      get_data_ajax();
    };
    tinvwl_add_to_wishlist.user_interacted = false;
    $.fn.tinvwl_get_wishlist_data();
    $(document).one('click keydown scroll', function () {
      tinvwl_add_to_wishlist.user_interacted = true;
      $.fn.tinvwl_get_wishlist_data();
    });

    /* Dynamic buttons */

    // Create an observer instance
    var observer = new MutationObserver(function (mutations) {
      var tinvwl_products = [];
      mutations.forEach(function (mutation) {
        var newNodes = mutation.addedNodes;

        // If there are new nodes added
        if (null !== newNodes) {
          var $nodes = $(newNodes);
          $nodes.each(function () {
            var $node = $(this),
              els = $node.find('.tinvwl_add_to_wishlist_button');
            if (els.length) {
              els.each(function () {
                var $this = $(this),
                  productData = $this.data('tinv-wl-product');
                if ('undefined' !== typeof productData && productData) {
                  tinvwl_products.push(productData);
                }
              });
            }
          });
        }
      });
      if (tinvwl_products.length) {
        $.fn.tinvwl_get_wishlist_data();
      }
    });

    // Configuration of the observer
    var config = {
      childList: true,
      subtree: true
    };
    var targetNode = document.body;

    // Start observing
    observer.observe(targetNode, config);
  });

  /* Storage Handling */

  // Check if HTML5 storage is supported
  var $supports_html5_storage = true;
  var hash_key = tinvwl_add_to_wishlist.hash_key;
  try {
    $supports_html5_storage = 'sessionStorage' in window && null !== window.sessionStorage;
    window.sessionStorage.setItem('ti', 'test');
    window.sessionStorage.removeItem('ti');
    window.localStorage.setItem('ti', 'test');
    window.localStorage.removeItem('ti');
  } catch (err) {
    // HTML5 storage is not supported
    $supports_html5_storage = false;
  }

  /**
   * Marks products based on the provided data.
   *
   * @param {object} data - The data containing information about wishlists, products, and stats.
   */
  function mark_products(data) {
    var g = '1' == window.tinvwl_add_to_wishlist['simple_flow'];
    var wishlists = [];
    for (var i in data.wishlists) {
      if (data.wishlists[i].hasOwnProperty('title') && '' === data.wishlists[i].title) {
        data.wishlists[i].title = window.tinvwl_add_to_wishlist['text_default_title'];
      }
      var wishlist = Object.assign({}, data.wishlists[i]);
      wishlists.push(wishlist);
    }
    $('a.tinvwl_add_to_wishlist_button').each(function () {
      $(this).removeClass('tinvwl-product-make-remove').removeClass('tinvwl-product-in-list').attr('data-tinv-wl-action', 'addto').attr('data-tinv-wl-list', JSON.stringify(wishlists));
      if (data && data.stats) {
        $(this).find('span.tinvwl-product-stats').remove();
      }
    });
    $('body').trigger('tinvwl_wishlist_mark_products', [data]);
    $.each(data.products, function (i, item) {
      var id = i,
        e = $('a.tinvwl_add_to_wishlist_button[data-tinv-wl-product="' + id + '"]');
      e.each(function () {
        var vid = parseInt($(this).attr('data-tinv-wl-productvariation')),
          vids = $(this).data('tinv-wl-productvariations') || [],
          j = false,
          d = [];
        for (var i in item) {
          if ('string' === typeof item[i]) {
            item[i] = JSON.parse(item[i]);
          }
          if (item[i].hasOwnProperty('title') && '' === item[i].title) {
            item[i].title = window.tinvwl_add_to_wishlist['text_default_title'];
          }
          if (item[i].hasOwnProperty('in') && Array.isArray(item[i].in) && (-1 < (item[i].in || []).indexOf(id) || -1 < (item[i].in || []).indexOf(vid) || vids.some(function (r) {
            return 0 <= (item[i].in || []).indexOf(r);
          }))) {
            j = true;
          }
          d.push(item[i]);
        }
        var mergedArray = [];
        var tempWishlists = wishlists.slice();
        for (var dIndex = 0; dIndex < d.length; dIndex++) {
          var dItem = d[dIndex];
          var wishlistsIndex = tempWishlists.findIndex(function (w) {
            return w.ID == dItem.ID;
          });
          if (-1 != wishlistsIndex) {
            tempWishlists.splice(wishlistsIndex, 1);
          }
          mergedArray.push(dItem);
        }
        for (var wishlistIndex = 0; wishlistIndex < tempWishlists.length; wishlistIndex++) {
          mergedArray.push(tempWishlists[wishlistIndex]);
        }
        $(this).attr('data-tinv-wl-list', JSON.stringify(mergedArray)).toggleClass('tinvwl-product-in-list', j).toggleClass('tinvwl-product-make-remove', j && g).attr('data-tinv-wl-action', j && g ? 'remove' : 'addto');
        $('body').trigger('tinvwl_wishlist_product_marked', [this, j, mergedArray]);
      });
    });
    if (data && data.stats && tinvwl_add_to_wishlist.stats) {
      $.each(data.stats, function (i, item) {
        var id = i,
          e = $('a.tinvwl_add_to_wishlist_button[data-tinv-wl-product="' + id + '"]');
        e.each(function () {
          $(this).attr('data-tinv-wl-product-stats', JSON.stringify(item));
          var vid = parseInt($(this).attr('data-tinv-wl-productvariation')),
            j = false;
          for (var i in item) {
            if (-1 < i.indexOf(vid)) {
              j = true;
              $('body').trigger('tinvwl_wishlist_product_stats', [this, j]);
              $(this).append('<span class="tinvwl-product-stats">' + item[i] + '</span>');
            }
          }
        });
      });
    }
    update_product_counter(data.counter, data.mini_wishlist);
  }

  /**
   * Sets the hash in local storage.
   *
   * @param {string} hash - The hash value to set.
   */
  function set_hash(hash) {
    if ($supports_html5_storage) {
      localStorage.setItem(hash_key, hash);
      sessionStorage.setItem(hash_key, hash);
      mark_products(JSON.parse(hash));
    }
  }

  /**
   * Updates the product counter and mini wishlist.
   *
   * @param {string|number} counter - The counter value.
   * @param {string} mini_wishlist - The HTML content of the mini wishlist.
   */
  function update_product_counter(counter, mini_wishlist) {
    // Hide counter if necessary
    if ('1' == window.tinvwl_add_to_wishlist['hide_zero_counter'] && 0 === counter) {
      counter = 'false';
    }

    // Add class to wishlist icon
    jQuery('i.wishlist-icon').addClass('added');

    // Update counter and icon label if counter is not 'false'
    if ('false' !== counter) {
      jQuery('.wishlist_products_counter_number, .theme-item-count.wishlist-item-count').html(counter);
      jQuery('i.wishlist-icon').attr('data-icon-label', counter);
    } else {
      // Remove counter and icon label if counter is 'false'
      jQuery('.wishlist_products_counter_number, .theme-item-count.wishlist-item-count').html('').closest('span.wishlist-counter-with-products').removeClass('wishlist-counter-with-products');
      jQuery('i.wishlist-icon').removeAttr('data-icon-label');
    }

    // Replace mini wishlist if provided
    if (mini_wishlist) {
      jQuery('.tinvwl-mini-wishlist-wrapper').replaceWith(mini_wishlist);
    }

    // Check if there are any products in the wishlist
    var has_products = !('0' == counter || 'false' == counter);

    // Toggle class based on the presence of products
    jQuery('.wishlist_products_counter').toggleClass('wishlist-counter-with-products', has_products);

    // Remove 'added' class from wishlist icon after a delay
    setTimeout(function () {
      jQuery('i.wishlist-icon').removeClass('added');
    }, 500);
  }

  /**
   * Sets up a focus trap for a specified element.
   * @param {HTMLElement} el - The element to trap the focus within.
   */
  function FocusTrap(el) {
    // Find all focusable elements within the specified element
    var inputs = $(el).find('select, input, textarea, button, a').filter(':visible');
    var firstInput = inputs.first();
    var lastInput = inputs.last();

    // Set focus on the first input and then blur it to prevent immediate focus
    firstInput.focus().blur();

    /**
     * Redirects the tab key press from the last input to the first input.
     * @param {KeyboardEvent} e - The keyboard event object.
     */
    lastInput.on('keydown', function (e) {
      if (9 === e.which && !e.shiftKey) {
        e.preventDefault();
        firstInput.focus();
      }
    });

    /**
     * Redirects the shift + tab key press from the first input to the last input.
     * @param {KeyboardEvent} e - The keyboard event object.
     */
    firstInput.on('keydown', function (e) {
      if (9 === e.which && e.shiftKey) {
        e.preventDefault();
        lastInput.focus();
      }
    });
  }
})(jQuery);
"use strict";

// Misc
(function ($) {
  $.fn.tinvwl_validate_createwishlist = function (so) {
    var sd = {
      class: {
        field: '#tinv_wishlist_create_field',
        button: '#tinv_wishlist_create_button',
        error: '#tinv_wishlist_create_error'
      },
      show_error: function show_error(c, msg) {
        $(this).find(c.error).html(msg).show();
        $(this).find(c.field).parent().addClass('tinvwl-has-error');
      },
      hide_error: function hide_error(c) {
        $(this).find(c.error).hide();
        $(this).find(c.field).parent().removeClass('tinvwl-has-error');
      },
      successfully_action: function successfully_action() {
        var form = $(this),
          formdata = {
            action: 'tinwl_create_wishlist'
          };
        if ($('a.tinvwl-create-wishlist-button').length) {
          formdata.redirect = true;
        }
        form.find('input, select, button').each(function () {
          switch ($(this).attr('type')) {
            case 'radio':
              if ($(this).is(':checked')) {
                formdata[$(this).attr('name')] = $(this).val().trim();
              }
              break;
            default:
              formdata[$(this).attr('name')] = $(this).val().trim();
          }
        });
        form.find('input[type=text]').val('');
        if (form.hasClass('tinv-modal-inner')) {
          form.parents('.tinv_wishlist_create_modal').removeClass('tinv-modal-open');
        }
        $.post(window.location.href, formdata, function (data) {
          $('.tinv-wishlist.woocommerce').eq(0).append(data);
        }, 'html');
      },
      validation: [{
        condition: function condition() {
          return 0 < $(this).val().trim().length;
        },
        message: tinvwl_create_wishlist.text_empty_name
      }]
    };
    var s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var form = $(this),
        field = form.find(s.class.field),
        button = form.find(s.class.button);
      if (0 === field.length || 0 === button.length) {
        return false;
      }
      button.on('click', function (e) {
        for (var i in s.validation) {
          var rule = s.validation[i];
          if (rule.condition.call(field)) {
            s.hide_error.call(form, s.class);
          } else {
            s.show_error.call(form, s.class, rule.message);
            e.preventDefault();
            return false;
          }
        }
        s.successfully_action.call(form);
      });
      field.on('focus', function () {
        s.hide_error.call(form, s.class);
      });
    });
  };
  $(document).ready(function () {
    $('.tinv_wishlist_create').tinvwl_validate_createwishlist();
    $.fn.tinvwl_modal = function (so) {
      var sd = {
          showClass: 'tinv-modal-open',
          modal: '.tinv-modal',
          onPrepare: function onPrepare() {
            if (!$('body > .tinv-wishlist').length) {
              $('body').append($('<div>').addClass('tinv-wishlist'));
            }
            $(this).appendTo('body > .tinv-wishlist');
          }
        },
        s = $.extend(true, {}, sd, so);
      return $(this).each(function () {
        var a = $(this),
          b = a.next(s.modal);
        s.onPrepare.call(b);
        a.on('click', function () {
          b.addClass(s.showClass);
        });
      });
    };
    $('.tinv-modal-btn').tinvwl_modal();
    if (!tinvwl_add_to_wishlist.notifications) {
      $('.tinvwl-button-notifications').each(function () {
        $(this).trigger('click');
        return false;
      });
    }
    $('.tinv-lists-nav').each(function () {
      if (!$(this).html().trim().length) {
        $(this).remove();
      }
    });
    $('body').on('click', '.social-buttons .social:not(.social-email,.social-whatsapp,.social-clipboard)', function (e) {
      var newWind = window.open($(this).attr('href'), $(this).attr('title'), 'width=420,height=320,resizable=yes,scrollbars=yes,status=yes');
      if (newWind) {
        newWind.focus();
        e.preventDefault();
      }
    });
    if ('undefined' !== typeof ClipboardJS) {
      var clipboard = new ClipboardJS('.social-buttons .social.social-clipboard', {
        text: function text(trigger) {
          return trigger.getAttribute('href');
        }
      });
      clipboard.on('success', function (e) {
        showTooltip(e.trigger, tinvwl_add_to_wishlist.tinvwl_clipboard);
      });
      var btns = document.querySelectorAll('.social-buttons .social.social-clipboard');
      for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener('mouseleave', clearTooltip);
        btns[i].addEventListener('blur', clearTooltip);
      }
    }
    $('body').on('click', '.social-buttons .social.social-clipboard', function (e) {
      e.preventDefault();
    });
    $('.tinvwl-select-all').on('click', function (e) {
      e.preventDefault();
      $(this).parent().parent().find('ul li input[type="checkbox"]').attr('checked', true);
      $(this).parent().parent().find('#tinv_wishlist_follow_error').hide();
    });
    $('.tinvwl-select-none').on('click', function (e) {
      e.preventDefault();
      $(this).parent().parent().find('ul li input[type="checkbox"]').attr('checked', false);
    });
    $('body').on('click', '.tinv-wishlist .tinv-overlay, .tinv-wishlist .tinv-close-modal, .tinv-wishlist .tinvwl_button_close', function (e) {
      e.preventDefault();
      $(this).parents('.tinv-modal:first').removeClass('tinv-modal-open');
      $('body').trigger('tinvwl_modal_closed', [this]);
    });
    $('body').on('click', '.tinv-wishlist .tinvwl-btn-onclick', function (e) {
      var url = $(this).data('url');
      if (url) {
        e.preventDefault();
        window.location = $(this).data('url');
      }
    });
    var navigationButton = $('.tinv-wishlist .navigation-button');
    if (navigationButton.length) {
      navigationButton.each(function () {
        var navigationButtons = $(this).find('> li');
        if (5 > navigationButtons.length) {
          navigationButtons.parent().addClass('tinvwl-btns-count-' + navigationButtons.length);
        }
      });
    }
    $('.tinv-login .showlogin').off('click').on('click', function (e) {
      e.preventDefault();
      $(this).closest('.tinv-login').find('.login').toggle();
    });
    $('.tinvwl_subscribe_form').on('submit', function (event) {
      if (!$(this).find('input[name="tinvwl_subscribes[]"]:checked').length) {
        event.preventDefault();
        $(this).find('#tinv_wishlist_follow_error').show();
      }
    });
    $('.tinvwl_subscribe_form input[type=checkbox]').on('change', function () {
      $(this).parents('form').eq(0).find('#tinv_wishlist_follow_error').hide();
    });
    $('.tinv-wishlist table.tinvwl-table-manage-list tfoot td').each(function () {
      $(this).toggle(!!$(this).children().not('.look_in').length || !!$(this).children('.look_in').children().length);
    });
    $('body').on('click', 'a.wishlist_title_more', function (e) {
      e.preventDefault();
      $(this).closest('.wishlist_list_titles').children('li').show();
      $(this).closest('li.wishlist_title_more').hide();
    });
  });
  $(window).on('load', function () {
    function tinvwl_pcounter_show() {
      var a = $(this).find('.wishlist_products_counter_wishlist');
      if (!a.length) {
        return;
      }
      a.show();
      var ao = a.offset(),
        al = ao.left,
        at = ao.top,
        aw = a.outerWidth(),
        ah = a.outerHeight(),
        la = parseFloat(a.css('left')),
        ta = parseFloat(a.css('top')),
        aol = al - la,
        aot = at - ta,
        _la = la,
        _ta = ta,
        ww = $(window).width(),
        dh = $(document).height(),
        os = 50,
        r = ww - aol - aw - os,
        l = os - aol,
        b = dh - aot - ah - os;
      if (ww <= aw) {
        _la = -1 * aol;
      } else if (0 > ww - (aw + os * 2)) {
        _la = (ww - aw) / 2 - aol;
      } else if (0 < l) {
        _la = l;
      } else if (0 > r) {
        _la = r;
      }
      if (dh < ah) {
        a.height(dh - a.outerHeight() + a.height());
        ah = a.outerHeight();
      }
      if (dh <= ah) {
        _ta = -1 * aot;
      } else if (0 > dh - (ah + os * 2)) {
        _ta = (dh - ah) / 2 - aot;
      } else if (0 > b) {
        _ta = b;
      }
      a.stop(true, true).show().css({
        left: _la,
        top: _ta,
        opacity: 0
      }).animate({
        opacity: 1
      }, 600);
    }
    function tinvwl_pcounter_hide() {
      var a = $(this).find('.wishlist_products_counter_wishlist');
      if (!a.length) {
        return;
      }
      $('.tinvwl-first-touch').removeClass('tinvwl-first-touch');
      $('.tinvwl-first-click').removeClass('tinvwl-first-click');
      if (a.is(':visible')) {
        a.stop(true, true).animate({
          opacity: 0
        }, 600, function () {
          $(this).hide();
        });
      }
    }
    var isMobile = localStorage.mobile || 1 < window.navigator.maxTouchPoints;
    if (isMobile) {
      $('body').on('click', '.wishlist_products_counter > a', function (e) {
        if (!$(this).hasClass('tinvwl-first-click') && $(this).closest('.wishlist_products_counter_dropdown').find('.wishlist_products_counter_wishlist').length) {
          e.preventDefault();
          $(this).addClass('tinvwl-first-click');
        }
      });
      var tinwl_swipe_trigger;
      $('body').on('touchstart', '.wishlist_products_counter_dropdown', function (e) {
        tinwl_swipe_trigger = false;
      }).on('touchmove', '.wishlist_products_counter_dropdown', function (e) {
        tinwl_swipe_trigger = true;
      }).on('touchend', '.wishlist_products_counter_dropdown', function (e) {
        if ($(this).hasClass('tinvwl-first-touch')) {
          if (!tinwl_swipe_trigger) {
            tinvwl_pcounter_hide.call($('.wishlist_products_counter_dropdown'), e);
          }
        } else {
          if ($(this).closest('.wishlist_products_counter_dropdown').find('.wishlist_products_counter_wishlist').length) {
            e.preventDefault();
            tinvwl_pcounter_show.call(this, e);
            $(this).addClass('tinvwl-first-touch');
          }
        }
      });
      $('body').on('touchend', ':not(.wishlist_products_counter_dropdown)', function (e) {
        if (0 === $(e.target).closest('.wishlist_products_counter_dropdown').length) {
          tinvwl_pcounter_hide.call($('.wishlist_products_counter_dropdown'), e);
        }
      });
    } else {
      $('.wishlist_products_counter_dropdown').hoverIntent({
        interval: 0,
        timeout: 100,
        over: tinvwl_pcounter_show,
        out: tinvwl_pcounter_hide
      });
    }
  });
})(jQuery);
function showTooltip(elem, msg) {
  elem.setAttribute('class', 'social social-clipboard tooltipped tooltipped-s');
  elem.setAttribute('aria-label', msg);
}
function clearTooltip(e) {
  e.currentTarget.setAttribute('class', 'social social-clipboard ');
  e.currentTarget.removeAttribute('aria-label');
}
"use strict";

// Wishlist table
(function ($) {
  //Prevent to submit wishlist table action
  $.fn.tinvwl_break_submit = function (so) {
    var sd = {
      selector: 'input, select, textarea',
      ifempty: true,
      invert: false,
      validate: function validate() {
        return $(this).val();
      },
      rule: function rule() {
        var form_elements = $(this).parents('form').eq(0).find(s.selector),
          trigger = s.invert;
        if (0 === form_elements.length) {
          return s.ifempty;
        }
        form_elements.each(function () {
          if (trigger && !s.invert || !trigger && s.invert) {
            return;
          }
          trigger = Boolean(s.validate.call($(this)));
        });
        return trigger;
      }
    };
    var s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      $(this).on('click', function (event) {
        var ss = [];
        if ('undefined' !== typeof $(this).attr('tinvwl_break_submit')) {
          ss = $(this).attr('tinvwl_break_submit').split(',');
        }
        if (-1 !== jQuery.inArray(s.selector, ss)) {
          ss = [];
        }
        if (!s.rule.call($(this)) && 0 === ss.length) {
          alert(window.tinvwl_add_to_wishlist['tinvwl_break_submit']);
          event.preventDefault();
        }
        ss.push(s.selector);
        $(this).attr('tinvwl_break_submit', ss);
        if (s.rule.call($(this))) {
          $(this).removeAttr('tinvwl_break_submit');
        }
      });
    });
  };
  $(document).ready(function () {
    // Wishlist table bulk action checkbox
    $('body').on('click', '.global-cb', function () {
      $(this).closest('table').eq(0).find('.product-cb input[type=checkbox], .wishlist-cb input[type=checkbox]').prop('checked', $(this).is(':checked'));
    });
    $('body').on('keyup keypress', 'tr.wishlist_item td.product-quantity input.qty', function (e) {
      var keyCode = e.keyCode || e.which;
      if (13 === keyCode) {
        e.preventDefault();
        $('button[value="product_update"]').trigger('click');
      }
    });
    $('body').on('keyup keypress', 'td.wishlist-name span.tinvwl-rename-input input', function (e) {
      var keyCode = e.keyCode || e.which;
      if (13 === keyCode) {
        e.preventDefault();
        $('button[value="manage_save"]').trigger('click');
      }
    });

    // Sorting products
    if ($.isFunction($.fn.sortable)) {
      $('.tinvwl-table-manage-list tbody').sortable({
        items: 'tr',
        cursor: 'move',
        axis: 'y',
        handle: 'td.product-sort',
        scrollSensitivity: 40
      });
    }
    var hash_key = tinvwl_add_to_wishlist.hash_key + '_refresh';

    // Refresh table
    $(document.body).on('tinvwl_wishlist_ajax_response', function (event, element, response) {
      // Check if the action is one of the specified values and the status is true
      if ((response.status || response.removed) && ['add_to_wishlist'].includes(response.action)) {
        // Run wishlist refresh
        if (response.wishlist && response.wishlist.share_key) {
          localStorage.setItem(hash_key, response.wishlist.share_key);
        }
      }
    });
  });
})(jQuery);