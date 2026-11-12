/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright  Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 *
 * @since   4.0.0
 */
(function (Joomla, document) {
  'use strict';

  if (!Joomla) {
    throw new Error('Joomla API not properly initialised');
  }

  var selectors = {
    clearBtn: '#jclear-cache-btn',
    cacheAnimationBtn: '.mod-extension-cache .j-cache-animation'
  };
  var theClearBtn = document.querySelector(selectors.clearBtn);
  var theCacheAnimationBtn = document.querySelector(selectors.cacheAnimationBtn);

  if (theClearBtn !== null) {
    theClearBtn.addEventListener('click', function (event) {
      event.preventDefault(); // $(this).data('id')

      var cacheurl = Joomla.getOptions('cacheurl');
      var cashsize = theClearBtn.getAttribute('data-size');
      var params = '';
      Joomla.request({
        url: cacheurl,
        method: 'POST',
        params: params,
        perform: true,
        onSuccess: function onSuccess(res) {
          var response = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
          var data = response.data;

          if (data.status) {
            // add disabled attribs and class after clear the cache
            theClearBtn.setAttribute('disabled', '');
            theClearBtn.className += ' disabled'; // get cache current size

            var currentCashSize = Math.round(cashsize);
            var clearedCashSize = 0; // cache size animation

            var interval = setInterval(function () {
              theCacheAnimationBtn.textContent = currentCashSize;

              if (currentCashSize <= clearedCashSize) {
                clearInterval(interval); // show success messages which getting from helper

                Joomla.renderMessages({
                  message: [data.messsage]
                });
              } // eslint-disable-next-line no-plusplus


              --currentCashSize;
            }, 1);
          }
        },
        onError: function onError(xhr) {
          Joomla.renderMessages({
            error: [xhr.response]
          });
        }
      });
    });
  }
})(window.Joomla, document);