"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function TInvWL($, h) {
  this.pf = 'tinvwl';
  this.g = '_';
  this.ho = h || false;
  this.n = 'TInvWL';
  this.aj_act = function (a) {
    return [this.pf, a].join(this.g);
  };
  this._csel = function (a, b) {
    var b = b || '.';
    return '{0}{1}{2}'.format(b, this.pf, a);
  };
  this._tm = function (a) {
    var c = $("script#{0}[type='text/template']".format(a));
    if (c.length) {
      return c.html();
    }
    return '';
  };
  this.formElm = function () {
    $(this._csel("-form-onoff")).tiwl_onoff();
    $('input[type=checkbox][tiwl-show], input[type=checkbox][tiwl-hide]').tiwl_onoffblock();
    $('[tiwl-required]').tiwl_requiredcondition();
    $('[tiwl-value][tiwl-show], [tiwl-value][tiwl-hide]').tiwl_byvalueblock();
    $('.tinvwl-form-preview-select button').on('click', this._preview_button);
    if (typeof $.fn.wpColorPicker !== 'undefined') {
      var calcLuminance = function calcLuminance(rgb) {
        var r = 0,
          g = 0,
          b = 0,
          a = 1;
        if (/^\#[0-9a-f]{6}$/i.test(rgb)) {
          var c = rgb.substring(1),
            _rgb = parseInt(c, 16);
          r = _rgb >> 16 & 0xff;
          g = _rgb >> 8 & 0xff;
          b = _rgb >> 0 & 0xff;
        } else if (/^rgba/i.test(rgb)) {
          _rgb = rgb.match(/(\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})\,\s*([0-9\.]+)/i) || [0, 0, 0, 0, 1];
          r = parseInt(_rgb[1]);
          g = parseInt(_rgb[2]);
          b = parseInt(_rgb[3]);
          a = parseFloat(_rgb[4]);
        } else if (/^rgb/i.test(rgb)) {
          _rgb = rgb.match(/(\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})/i) || [0, 0, 0, 0, 1];
          r = parseInt(_rgb[1]);
          g = parseInt(_rgb[2]);
          b = parseInt(_rgb[3]);
        }
        return 0.2126 * r + 0.7152 * g + 0.0722 * b + 255 * (1 - a);
      };
      var formColor = this._csel("-form-color");
      $(formColor).each(function () {
        var picker = $(this);
        var pickerWrap = $(this).closest('.tinvwl-color-picker');
        var eyedropper = pickerWrap.find('.tinvwl-eyedropper');
        picker.css({
          'background-color': picker.val(),
          'width': ''
        });
        if (calcLuminance(picker.val()) > 175) {
          picker.css('color', '#000000');
        }
        picker.iris({
          mode: 'hsv',
          target: $(this).parent().parent(),
          change: function change(event, ui) {
            if (calcLuminance(ui.color.toCSS()) > 175) {
              $(this).css('color', '#000000');
            } else {
              $(this).css('color', '');
            }
            $(this).css({
              'background-color': ui.color.toCSS(),
              'width': ''
            });
          }
        });
        pickerWrap.on('click', '.iris-square-value', function (e) {
          e.preventDefault();
          picker.iris('toggle');
        });
        eyedropper.on('click', function (e) {
          e.preventDefault();
          picker.iris('show');
        });
        picker.on('focusin', function () {
          picker.iris('show');
        });
      });
      $(document).on('click', function (e) {
        if (!$(e.target).is(formColor + ', .iris-picker, .iris-picker-inner, .iris-slider-offset, .tinvwl-eyedropper, .tinvwl-eyedropper .ftinvwl-eyedropper')) {
          $(formColor).iris('hide');
        } else {
          $(formColor).not($(e.target).closest('.tinvwl-color-picker').find(formColor)).iris('hide');
        }
      });
    }
    $('.tinvwl-color-important-picker').each(function () {
      var a = $(this).find('.tinvwl-color-important_color'),
        b = $(this).find('.tinvwl-color-important_important'),
        c = $(this).find('.tinvwl-color-important_value'),
        t = null;
      c.val(a.val() + (b.is(':checked') ? ' !important' : ''));
      a.iris({
        mode: 'hsv',
        target: $(this).parent().parent(),
        change: function change(event, ui) {
          if (calcLuminance(ui.color.toCSS()) > 175) {
            $(this).css('color', '#000000');
          } else {
            $(this).css('color', '');
          }
          $(this).css({
            'background-color': ui.color.toCSS(),
            'width': ''
          });
          c.val(a.val() + (b.is(':checked') ? ' !important' : ''));
          if (t) {
            clearTimeout(t);
          }
          t = setTimeout(function () {
            c.val(a.val() + (b.is(':checked') ? ' !important' : ''));
          }, 200);
        }
      });
      a.change(function () {
        c.val(a.val() + (b.is(':checked') ? ' !important' : ''));
      });
      b.change(function () {
        c.val(a.val() + (b.is(':checked') ? ' !important' : ''));
      });
    });
    $('.tinvwl-box-shadow-elements').each(function () {
      var change_full_value = function change_full_value() {
        var n = $(this).attr('name').replace(/_(color_color|color_important|inset|hshadow|vshadow|blur|spread)/i, ''),
          ins = $('select[name=' + n + '_inset]').val() || '',
          hs = $('input[name=' + n + '_hshadow]').val() || 0,
          vs = $('input[name=' + n + '_vshadow]').val() || 0,
          b = $('input[name=' + n + '_blur]').val() || 0,
          s = $('input[name=' + n + '_spread]').val() || 0,
          c = $('input[name=' + n + '_color_color]').val() || '#000000',
          i = $('input[name=' + n + '_color_important]').is(':checked'),
          v = ins + ' ' + hs + 'px ' + vs + 'px ' + b + 'px ' + s + 'px ' + c + (i ? ' !important' : '');
        $('input[name=' + n + ']').val(v.trim());
      };
      $(this).find('input[name$=_color_color]').iris({
        mode: 'hsv',
        target: $(this).parent().parent(),
        change: function change(event, ui) {
          if (calcLuminance(ui.color.toCSS()) > 175) {
            $(this).css('color', '#000000');
          } else {
            $(this).css('color', '');
          }
          $(this).css({
            'background-color': ui.color.toCSS(),
            'width': ''
          });
          change_full_value.call(this);
        }
      });
      $(this).find('input[name$=_color_important],select[name$=_inset],input[name$=_hshadow],input[name$=_vshadow],input[name$=_blur],input[name$=_spread]').change(change_full_value);
      $(this).find('select[name$=_inset]').trigger('change');
    });
  };
  this.getform_section = function (a) {
    var c = {};
    $('[name^="' + a + '"]').each(function () {
      var d = $(this),
        e = d.attr('type'),
        f = d.attr('name'),
        g = d.val();
      if ('checkbox' === e || 'radio' === e) {
        if (d.is(':checked')) {
          c[f] = g;
        }
      } else {
        c[f] = g;
      }
    });
    return c;
  };
  this.presave_section = function (a) {
    var b = {};
    if ('object' === _typeof(a)) {
      for (var i in a) {
        if ('string' === typeof a[i]) {
          var c = this.getform_section(a[i]);
          for (var j in c) {
            b[j] = c[j];
          }
        }
      }
    } else if ('string' === typeof a) {
      b = this.getform_section(a);
    }
    $.ajax({
      url: window.location.href,
      async: false,
      method: 'POST',
      data: b
    });
  };
  this.preview_button = function (a) {
    var a = $(a);
    this.presave_section(['notifications_style', 'promotional_email']);
    window.open(a.attr('href'), '', 'width=600,height=900,resizable=yes,scrollbars=yes,status=yes');
  };
  this.wizard_page = function (a) {
    $(a).find('select').change(this._wizard_page_ch);
    this.wizard_page_ch($(a).find('select'));
  };
  this.wizard_page_ch = function (a) {
    var a = $(a),
      b = a.parent(this._csel('-page-select')),
      c = b.find('input[type=hidden]').val(),
      d = b.find(this._csel('-error-icon')),
      e = b.find(this._csel('-error-desc'));
    if ('' === a.val()) {
      if (0 == c) {
        b.addClass('tinvwl-error');
        d.show();
        e.show();
      }
      return;
    }
    b.removeClass('tinvwl-error');
    d.hide();
    e.hide();
  };
  this.pageElm = function () {
    $(this._csel('-header', 'div.')).prependTo('#wpbody-content');
    var a = $(this._csel('-style-template select'));
    if (0 < a.length) {
      this.changetmpl(a);
      a.change(this._changetmpl);
    }
    $(this._csel('-page-select')).each(this._wizard_page);
    $(this._csel('-preparepromo-preview')).each(this._promotional_preview);
    $('.bulkactions [type=submit]').each(this._control_bulkactions);
    $('.action-search [type=submit]').each(this._control_search);
  };
  this.control_bulkactions = function (a) {
    $(a).on('click', this._control_bulkactions_ck);
  };
  this.control_bulkactions_ck = function (a, b) {
    var a = $(a),
      c = a.parents('.bulkactions').eq(0).find('[name=action]'),
      d = a.parents('form').eq(0);
    if (c) {
      if ('-1' === c.val()) {
        b.preventDefault();
      } else {
        if (!d.find('input[type=checkbox]:checked').length) {
          b.preventDefault();
        }
      }
    }
  };
  this.control_search = function (a) {
    $(a).on('click', this._control_search_ck);
  };
  this.promotional_preview = function (a) {
    $(a).on('click', this._promotional_preview_ck);
  };
  this.promotional_preview_ck = function (a) {
    var a = $(a),
      b = a.attr('data-type'),
      c = a.parents('form').eq(0),
      d = {
        data_type: b,
        action: 'prepare_promotion'
      };
    c.find('input, textarea, select').each(function () {
      d[$(this).attr('name')] = $(this).val();
    });
    $.post(ajaxurl, d, this._promotional_preview_post, 'json');
  };
  this.promotional_preview_post = function (a, b) {
    if ('object' === _typeof(b) && b.hasOwnProperty('url')) {
      window.open(b.url, '', 'width=600,height=900,resizable=yes,scrollbars=yes,status=yes');
    }
  };
  this.changetmpl = function (a) {
    var a = $(a);
    $(this._csel('-style-options')).html('');
    $(this._csel('-style-options')).next('input[type=hidden]').remove();
    $(this._csel('-style-options')).next('input[type=hidden]').remove();
    $.post(ajaxurl, {
      action: 'selecttemplate',
      selected: a.val()
    }, this._changetmpl_post);
  };
  this.changetmpl_post = function (a, b) {
    $(this._csel('-style-options')).replaceWith(b);
    this.formElm();
  };
  this.Run = function () {
    this.formElm();
    this.pageElm();
  };
  this.cg = function () {
    var n = this.n;
    if (this.ho) {
      var t = new Date();
      n = n + t.getFullYear() + t.getMonth() + t.getDate();
    }
    window[n] = this;
  };
  this.cg();
  if (!String.prototype.format) {
    String.prototype.format = function () {
      var args = arguments;
      return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] !== 'undefined' ? args[number] : match;
      });
    };
  }
  (function (o) {
    var n = o.n,
      ho = o.ho,
      c = '';
    if (ho) {
      c = 't=new Date(),n=n+t.getFullYear()+t.getMonth()+t.getDate(),';
    }
    for (var i in o) {
      if ('function' === typeof o[i] && '_' !== i[0] && !o.hasOwnProperty('_' + i)) {
        eval("o._" + i + "=function(a,b,c,d){var n='" + n + "'," + c + "o=window[n]||null;if (o) {return o." + i + "(this,a,b,c,d);};};");
      }
    }
  })(this);
}
(function ($) {
  $.fn.tiwl_onoff = function (so) {
    var sd = {
        value: {
          on: '',
          off: ''
        },
        class: 'tiwlform-onoff',
        wrap: 'container',
        button: 'button'
      },
      s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
        b1 = $('<div>').attr({
          class: s.class + '-' + s.button
        }),
        d1c = s.class + '-' + s.wrap,
        d1 = $('<div>').attr({
          id: a.attr('id') + '_' + s.wrap,
          class: d1c
        });
      if (!a.is('input')) {
        return a;
      }
      d1.attr('class', d1.attr('class') + ' ' + a.attr('class'));
      if (a.is(':disabled')) {
        d1.toggleClass('disabled', a.is(':disabled'));
        a.prop('disabled', false);
      }
      d1.toggleClass('checked', a.is(':checked'));
      a.hide().removeAttr('class').wrap(d1).before(b1);
      d1 = a.parent();
      a.on('change', function (e) {
        if (d1.hasClass('disabled')) {
          return e.preventDefault();
        }
        d1.toggleClass('checked', $(this).is(':checked'));
      });
      d1.on('click', function (e) {
        if (d1.hasClass('disabled')) {
          return e.preventDefault();
        }
        if (a.is(':enabled') && d1.hasClass('checked') === a.is(':checked')) {
          a.click();
        }
      });
      return a;
    });
  };
  $.fn.tiwl_onoffblock = function (so) {
    var sd = {
        onEachElm: function onEachElm() {},
        isChecked: function isChecked() {
          return $(this).is(':checked');
        }
      },
      s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
        setAction = function setAction() {
          var o = $(this),
            o_show = o.attr('tiwl-show'),
            o_hide = o.attr('tiwl-hide'),
            o_ch = s.isChecked.call(o),
            doAction = function doAction(o_, on) {
              o_ = o_.match(/[\w\d-\>\.\#\:\=\[\]]+/igm) || [];
              $.each(o_, function (k, v) {
                s.onEachElm.call($(v).toggle(on));
              });
            };
          if ('string' === typeof o_show) {
            doAction(o_show, o_ch);
          }
          if ('string' === typeof o_hide) {
            doAction(o_hide, !o_ch);
          }
          return o;
        };
      if (!a.is('input') || 'checkbox' != a.attr('type')) {
        return a;
      }
      $(this).on('change', setAction);
      return setAction.call(a);
    });
  };
  $.fn.tiwl_requiredcondition = function (so) {
    var sd = {
        attribute: 'tiwl-required',
        onShow: function onShow() {
          return $(this).show();
        },
        onHide: function onHide() {
          return $(this).hide();
        },
        onToggle: function onToggle(e, toggle) {
          if (toggle) {
            $(this).trigger('tiwl_condition.show');
          } else {
            $(this).trigger('tiwl_condition.hide');
          }
        },
        onCheck: function onCheck(value) {
          var a = $(this),
            r = false;
          if ('checkbox' === a.attr('type') || 'radio' === a.attr('type')) {
            a = a.filter(':checked');
            if (!a.length) {
              return !value;
            }
            a.each(function () {
              if ('object' === _typeof(value)) {
                for (var i in value) {
                  if (a.val() == value[i]) {
                    r = true;
                  }
                }
              } else {
                r = r || $(this).val() == value;
              }
            });
            return r;
          }
          a.each(function () {
            if ('object' === _typeof(value)) {
              for (var i in value) {
                if (a.val() == value[i]) {
                  r = true;
                }
              }
            } else {
              r = r || $(this).val() == value;
            }
          });
          return r;
        },
        condition: {},
        fullcheck: true
      },
      s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
        c = a.attr(s.attribute) || s.condition;
      if (!c) {
        return;
      }
      if ('string' === typeof c) {
        try {
          c = JSON.parse(c);
        } finally {
          if (!c) {
            return;
          }
        }
      }
      a.on('tiwl_condition.show', s.onShow);
      a.on('tiwl_condition.hide', s.onHide);
      a.on('tiwl_condition.toggle', s.onToggle);
      a.on('tiwl_condition.check', function (e, el) {
        var r = null,
          ca;
        if (s) {
          var _r = true,
            _c = false;
          for (var i in c) {
            ca = $('[name="' + i + '"]');
            if (ca.length) {
              _r = _r && s.onCheck.call(ca, c[i]);
              _c = _c || true;
            }
            if (!_r) {
              break;
            }
          }
          if (_c) {
            r = _r;
          }
        } else {
          if (c.hasOwnProperty(el)) {
            ca = $('[name="' + el + '"]');
            if (ca.length) {
              r = s.onCheck.call(ca, c[el]);
            }
          }
        }
        if (null !== r) {
          $(this).trigger('tiwl_condition.toggle', [r]);
        }
      });
      for (var i in c) {
        $('body').on('click change', '[name="' + i + '"]', function (e) {
          a.trigger('tiwl_condition.check', [$(this).attr('name')]);
        });
      }
      a.trigger('tiwl_condition.check');
    });
  };
  $.fn.tiwl_byvalueblock = function (so) {
    var sd = {
        onEachElm: function onEachElm() {},
        onClick: function onClick() {
          return $(this).val() == $(this).attr('tiwl-value');
        }
      },
      s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
        setAction = function setAction(s) {
          var o = $(this),
            o_show = o.attr('tiwl-show'),
            o_hide = o.attr('tiwl-hide'),
            o_ch = s.onClick.call(o),
            doAction = function doAction(o_, on) {
              o_ = o_.match(/[\w\d-\>\.\#\:\=\[\]]+/igm) || [];
              $.each(o_, function (k, v) {
                s.onEachElm.call($(v).toggle(on));
              });
            };
          if ('string' === typeof o_show) {
            doAction(o_show, o_ch);
          }
          if ('string' === typeof o_hide) {
            doAction(o_hide, !o_ch);
          }
          return o;
        };
      if (!a.is('input') && !a.is('select')) {
        return a;
      }
      $(this).on('change', function () {
        setAction.call(this, s);
      });
      return setAction.call(a, s);
    });
  };
  $.fn.tiwl_resetval = function (so) {
    var sd = {
        events: 'click',
        value: null,
        onAction: function onAction() {},
        onCancelAction: function onCancelAction() {},
        onContinueAction: function onContinueAction() {}
      },
      s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
        default_a = s.value || a.val();
      a.on(s.events, function (e) {
        s.onAction.call(this, e, default_a);
      });
      a.on('value_reset', function (e) {
        s.onCancelAction.call(this, e, default_a);
        $(this).val(default_a);
      });
      a.on('value_leave', s.onContinueAction);
    });
  };
  var a = new TInvWL($);
  $(document).ready(function () {
    a.Run();
    jQuery('input[name="general-multi"]').change(function () {
      var o = jQuery(this),
        a = o.is(':checked'),
        b = jQuery('input[name="general-show_notice"]'),
        c = jQuery('input[name="general-simple_flow"]');
      if (a && !b.is(':checked')) {
        b.click().trigger('change');
      }
      if (a && c.is(':checked')) {
        c.click().trigger('change');
      }
      b.closest('.tiwlform-onoff-container').toggleClass('disabled', a);
    }).change();
    jQuery('input[name="general-show_notice"]').change(function () {
      var o = jQuery(this),
        a = !o.is(':checked'),
        b = jQuery('input[name="general-redirect_require_login"]');
      if (a && !b.is(':checked')) {
        b.click().trigger('change');
      }
      b.closest('.tiwlform-onoff-container').toggleClass('disabled', a);
    }).change();
    $('.tablenav').each(function () {
      var tablenav = $(this);
      if (!$.trim(tablenav.find('.alignleft').html()).length) {
        tablenav.find('.alignleft').remove();
      }
      if (!$.trim(tablenav.find('.alignright').html()).length || tablenav.find('.tablenav-pages').is('.one-page, .no-pages')) {
        tablenav.find('.alignright').remove();
        tablenav.find('.tinv-wishlist-clear').remove();
      }
      if (!$.trim(tablenav.html()).length) {
        tablenav.remove();
      }
    });
    $('.tablenav .bulkactions select').addClass('tinvwl-select grey').wrap('<span class="tinvwl-select-wrap">').parent().append('<span class="tinvwl-caret"><span></span></span>');
    $('.tablenav .bulkactions .button.action, .tablenav #search-submit').removeClass('button').addClass('tinvwl-btn grey');
    $('.tinvwl-modal-btn').on('click', function () {
      $(this).next('.tinvwl-modal').addClass('tinvwl-modal-open');
    });
    $('.tinvwl-overlay, .tinvwl-close-modal, .tinvwl_button_close').on('click', function (e) {
      e.preventDefault();
      $(this).parents('.tinvwl-modal:first').removeClass('tinvwl-modal-open');
    });
    if (typeof $.fn.popover !== 'undefined') {
      var popover = $('.tinvwl-help');
      popover.popover({
        content: function content() {
          return $(this).closest('.tinvwl-info-wrap').find('.tinvwl-info-desc').html();
        }
      });
      popover.on('click', function () {
        $(this).popover('toggle');
      });
      popover.on('focusout', function () {
        $(this).popover('hide');
      });
      $(window).on('resize', function () {
        popover.popover('hide');
      });
    }
    $('select[name=notification_template-TInvWL_Public_Email_Promotional]').tiwl_resetval({
      events: 'change',
      onAction: function onAction(e, dv) {
        var a = $(this),
          b = $('.tinvwl-notification-preview-emails');
        if (dv == a.val()) {
          return e.preventDefault();
        }
        b.addClass('tinvwl-modal-open');
        b.find('.tinvwl-continue').click(function () {
          a.trigger('value_leave');
        });
        b.find('.tinvwl-close').click(function () {
          a.trigger('value_reset');
        });
      }
    });
    $('body').on('click', '.tinvwl-confirm-reset', function (e) {
      e.preventDefault();
      var a = confirm(tinvwl_comfirm.text_comfirm_reset);
      if (a) {
        $(this).removeClass('tinvwl-confirm-reset').trigger('click');
      }
    });
  });
})(jQuery);