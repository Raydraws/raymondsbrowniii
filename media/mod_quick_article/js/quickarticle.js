/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright  Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
(function (Joomla, document) {
    'use strict';
  
    if (!Joomla) {
      throw new Error('Joomla API not properly initialised');
    }
  
    var selectors = {
      form: '#form-quick-article',
      submitBtn: '#mod-quick-article-submit',
      clearBtn: '#mod-quick-article-clear',
      descriptionField: '#mod-quick-article-description',
      titleField: '#mod-quick-article-title'
    };
  
    function serialiseForm(form) {
      var obj = [];
      var elements = form.querySelectorAll('input, select, textarea');
  
      for (var i = 0, l = elements.length; i < l; i += 1) {
        var _elements$i = elements[i],
            name = _elements$i.name,
            value = _elements$i.value;
  
        if (name) {
          if (elements[i].type === 'checkbox' && elements[i].checked === true || elements[i].type !== 'checkbox') {
            obj.push("".concat(name.replace('[', '%5B').replace(']', '%5D'), "=").concat(encodeURIComponent(value)));
          }
        }
      }
  
      return obj.join('&');
    }
  
    var theForm = document.querySelector(selectors.form);
    var theClearnBtn = document.querySelector(selectors.clearBtn);
    theClearnBtn.addEventListener('click', function (event) {
      event.preventDefault();
      document.querySelector(selectors.descriptionField).value = '';
      document.querySelector(selectors.titleField).value = '';
    });
    theForm.addEventListener('submit', function (event) {
      event.preventDefault();
      var saveurl = Joomla.getOptions('saveurl');
      var editurl = Joomla.getOptions('editurl');
      var data = serialiseForm(theForm);
      Joomla.request({
        url: saveurl,
        method: 'POST',
        data: data,
        perform: true,
        onSuccess: function onSuccess(res) {
          var response = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
  
          if (response) {
            window.location.href = "".concat(editurl).concat(response.data);
          }
        },
        onError: function onError(xhr) {
          Joomla.renderMessages({
            error: [xhr.response]
          });
        }
      });
    });
  })(window.Joomla, document);