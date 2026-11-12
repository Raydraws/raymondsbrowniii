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
((Joomla, document) => {
  'use strict';

  if (!Joomla) {
    throw new Error('Joomla API not properly initialised');
  }

  const selectors = {
    clearBtn: '#jclear-cache-btn',
    cacheAnimationBtn: '.mod-extension-cache .j-cache-animation'
  };
  const theClearBtn = document.querySelector(selectors.clearBtn);
  const theCacheAnimationBtn = document.querySelector(selectors.cacheAnimationBtn);

  if (theClearBtn !== null) {
    theClearBtn.addEventListener('click', event => {
      event.preventDefault(); // $(this).data('id')

      const cacheurl = Joomla.getOptions('cacheurl');
      const cashsize = theClearBtn.getAttribute('data-size');
      const params = '';
      Joomla.request({
        url: cacheurl,
        method: 'POST',
        params,
        perform: true,

        onSuccess(res) {
          const response = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
          const {
            data
          } = response;

          if (data.status) {
            // add disabled attribs and class after clear the cache
            theClearBtn.setAttribute('disabled', '');
            theClearBtn.className += ' disabled'; // get cache current size

            let currentCashSize = Math.round(cashsize);
            const clearedCashSize = 0; // cache size animation

            const interval = setInterval(() => {
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

        onError(xhr) {
          Joomla.renderMessages({
            error: [xhr.response]
          });
        }

      });
    });
  }
})(window.Joomla, document);