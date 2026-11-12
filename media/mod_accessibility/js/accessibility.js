/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

/* eslint-disable max-len */

/* eslint-disable no-use-before-define */

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
    a11yCollapseBtn: '#accessibility-collapse-control',
    a11ySidebar: '.accessibility-sidebar',
    a11yScalingWrap: '.accessibility-scaling',
    a11yActionBtn: '.accessible-action-btn',
    a11yMagnifierBtn: '[data-type="magnifier"]',
    a11yMouseHighlighter: '[data-type="mouse-highlighter"]',
    a11yResetBtn: '#accessible-reset-btn',
    bodyRoot: 'body',
    htmlRoot: 'html'
  };
  var a11yCollapseBtn = document.querySelector(selectors.a11yCollapseBtn);
  var a11ySidebar = document.querySelector(selectors.a11ySidebar);
  var a11yScalingWrap = document.querySelector(selectors.a11yScalingWrap);

  var a11yActionBtn = _toConsumableArray(document.querySelectorAll(selectors.a11yActionBtn)); // const a11yMagnifierBtn = document.querySelector(selectors.a11yMagnifierBtn);


  var mouseHighlighter = document.querySelector(selectors.a11yMouseHighlighter);
  var a11yResetBtn = document.querySelector(selectors.a11yResetBtn);
  var bodyRoot = document.querySelector(selectors.bodyRoot);
  var htmlRoot = document.querySelector(selectors.htmlRoot);
  var actionStatus = false;
  var cursorPointer = document.querySelector('.a11y-cursor-pointer');
  var cursorPointerInner = document.querySelector('.a11y-cursor-pointer-inner');
  var supportsCssVariables = window.CSS && window.CSS.supports && window.CSS.supports('--fake-var', 0);
  
  // localStorage key for accessibility settings
  var STORAGE_KEY = 'joomla_a11y_settings';
  
  // Save accessibility settings to localStorage
  function saveA11ySettings() {
    try {
      var settings = {
        fontPercent: window.fontPercent || 0,
        grayscale: htmlRoot.classList.contains('a11y-grayscale'),
        contrast: htmlRoot.classList.contains('a11y-enable-contrast'),
        nomotion: bodyRoot.classList.contains('a11y-no-motion'),
        bbcursor: bodyRoot.classList.contains('a11y-big-black-cursor'),
        bhcursor: bodyRoot.classList.contains('a11y-big-white-cursor'),
        magnifier: bodyRoot.classList.contains('a11y-magnifier'),
        mouseHighlighter: bodyRoot.classList.contains('a11y-mouse-highlighter')
      };
      localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
    } catch (e) {
      // localStorage not available or quota exceeded
      console.warn('Could not save accessibility settings to localStorage:', e);
    }
  }
  
  // Load accessibility settings from localStorage
  function loadA11ySettings() {
    try {
      var saved = localStorage.getItem(STORAGE_KEY);
      if (!saved) {
        return null;
      }
      return JSON.parse(saved);
    } catch (e) {
      // localStorage not available or corrupted data
      console.warn('Could not load accessibility settings from localStorage:', e);
      return null;
    }
  }
  
  // Clear accessibility settings from localStorage
  function clearA11ySettings() {
    try {
      localStorage.removeItem(STORAGE_KEY);
    } catch (e) {
      console.warn('Could not clear accessibility settings from localStorage:', e);
    }
  }
  
  // Accessibility sidebar controller

  a11yCollapseBtn.addEventListener('click', function (e) {
    e.preventDefault();
    a11ySidebar.classList.toggle('a11y-active');
  }); // Disapear sidebar if click outside of the siderbar

  document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('header-item-link') && !e.target.closest('.mod-accessibility') && a11ySidebar.classList.contains('a11y-active')) {
      a11ySidebar.classList.remove('a11y-active');
    }
  }); // all action buttons

  a11yActionBtn.forEach(function (actionBtn) {
    actionBtn.addEventListener('click', function (e) {
      e.preventDefault(); // let actionStatus    = false;

      var actionType = actionBtn.getAttribute('data-type'); // const cuurentActionStatus = checkActionStatus(actionBtn);
      // if accessiblity action type grascale

      if (actionType === 'grayscale') {
        // reset contrast
        if (htmlRoot.classList.contains('a11y-enable-contrast')) {
          htmlRoot.classList.remove('a11y-enable-contrast');
          document.querySelector('[data-type="contrast"]').classList.remove('a11y-active');
        } // apply/remove grayscale


        htmlRoot.classList.toggle('a11y-grayscale');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity action type big black cursor


      if (actionType === 'bbcursor') {
        // reset white cursor
        if (bodyRoot.classList.contains('a11y-big-white-cursor')) {
          bodyRoot.classList.remove('a11y-big-white-cursor');
          document.querySelector('[data-type="bhcursor"]').classList.remove('a11y-active');
        } // apply/remove black cursor


        bodyRoot.classList.toggle('a11y-big-black-cursor');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity action type big white cursor


      if (actionType === 'bhcursor') {
        // reset black cursor
        if (bodyRoot.classList.contains('a11y-big-black-cursor')) {
          bodyRoot.classList.remove('a11y-big-black-cursor');
          document.querySelector('[data-type="bbcursor"]').classList.remove('a11y-active');
        } // apply/remove white cursor


        bodyRoot.classList.toggle('a11y-big-white-cursor');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity action type no motion


      if (actionType === 'nomotion') {
        bodyRoot.classList.toggle('a11y-no-motion');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity action type contrast


      if (actionType === 'contrast') {
        // reset grayscale
        if (htmlRoot.classList.contains('a11y-grayscale')) {
          htmlRoot.classList.remove('a11y-grayscale');
          document.querySelector('[data-type="grayscale"]').classList.remove('a11y-active');
        } // apply/remove contast


        htmlRoot.classList.toggle('a11y-enable-contrast');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity type magnifier


      if (actionType === 'magnifier') {
        magnifierInit(); // apply/remove magnifier

        bodyRoot.classList.toggle('a11y-magnifier');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      } // if accessiblity type mouse highlighter


      if (actionType === 'mouse-highlighter') {
        // apply/remove magnifier
        if (!actionBtn.classList.contains('a11y-active')) {
          pointerAppear();
        } else {
          pointerDisapear();
        } // add class to body


        bodyRoot.classList.toggle('a11y-mouse-highlighter');
        actionStatus = true;
        changeAcitonStatus(actionBtn, actionType);
        saveA11ySettings();
      }
    });
  }); // check action status

  function checkActionStatus(action) {
    var actionType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

    if (actionType === 'checkbox') {
      return action.checked;
    }

    if (action.classList.contains('a11y-active')) {
      return true;
    }

    return false;
  } // *** START:: Text magnifier *** //


  function magnifierInit() {
    activateMagnifier();
  }

  function activateMagnifier() {
    var magnifiableTags = ['p', 'a', 'span', 'li', 'ul', 'img', 'button'];
    var magSelectors = magnifiableTags.reduce(function (accumulator, magnifiableTag) {
      var getSelectors = _toConsumableArray(document.querySelectorAll("".concat(magnifiableTag)));

      return [].concat(_toConsumableArray(accumulator), _toConsumableArray(getSelectors));
    }, []);
    magSelectors.forEach(function (selector) {
      selector.addEventListener('mouseenter', showMagnifier);
      selector.addEventListener('mouseleave', removeMagnifier);
    });
  }

  function showMagnifier(event) {
    event.preventDefault(); // if mouse magnifier isn't enabled then return false

    if (checkActionStatus(document.querySelector('[data-type="magnifier"]')) === false) {
      return false;
    }

    var txt;

    if (event.target.title) {
      txt = event.target.title;
    } else {
      txt = event.target.innerText.replace(/<[^>]*>/g, '');
    }

    if (txt !== '') {
      generateMagnifier(txt, event.target);
    }

    return true;
  }

  function generateMagnifier(text, item) {
    var itemRect = item.getBoundingClientRect();
    var magnifierWrap = document.createElement('span');
    magnifierWrap.classList.add('a11y-magnified-text');
    magnifierWrap.appendChild(document.createTextNode(text));
    var firstChild = document.body.firstChild;
    firstChild.parentNode.insertBefore(magnifierWrap, firstChild); // positioning magnifier

    magnifierWrap.style.top = "".concat(Math.round(itemRect.bottom), "px");
    magnifierWrap.style.left = "".concat(Math.round(itemRect.left + itemRect.width / 2), "px");
  }

  function removeMagnifier() {
    var magnifiers = document.querySelectorAll('.a11y-magnified-text');
    magnifiers.forEach(function (magnifier) {
      document.body.removeChild(magnifier);
    });
  } // *** END:: Text magnifier *** //
  // *** START:: Mouse highligter *** //
  // mouse highlighter default options


  var options = {
    movementSmoothness: 2,
    currentX: 0,
    currentY: 0,
    currentScale: 1,
    clientX: 0,
    clientY: 0,
    clientScale: 1,
    target: null
  };

  function pointerAppear() {
    bodyRoot.addEventListener('mousemove', pointerInit);
    bodyRoot.addEventListener('mousemove', pointerMove); // mouse enter and leave on the screen

    bodyRoot.addEventListener('mouseenter', pointerInit);
    bodyRoot.addEventListener('mouseleave', pointerDisapear);
  }

  function pointerMove(e) {
    // if mouse highlighter isn't enabled then return false
    if (checkActionStatus(document.querySelector('[data-type="mouse-highlighter"]')) === false) {
      return false;
    }

    if (e.clientX) {
      // position of the mouse based on the window
      var mouseX = e.clientX;
      var mouseY = e.clientY; // get the target position, usualy the mouse position if not snapping

      options.clientX = options.target ? options.target.x : mouseX; // mouse X position or snap target

      options.clientY = options.target ? options.target.y : mouseY; // mouse Y position or snap target
    }

    return true;
  }

  function repeatOften() {
    // set positions on option
    options.currentX = +(options.currentX + (options.clientX - options.currentX) / options.movementSmoothness).toFixed(2);
    options.currentY = +(options.currentY + (options.clientY - options.currentY) / options.movementSmoothness).toFixed(2);

    if (supportsCssVariables) {
      // set the css variables
      cursorPointer.style.setProperty('--x', "".concat(options.currentX, "px"));
      cursorPointer.style.setProperty('--y', "".concat(options.currentY, "px"));
    } else {
      cursorPointer.style.transform = "translate3d(".concat(options.currentX, "px,").concat(options.currentY, "px,0)");
      cursorPointerInner.style.transform = "scale(".concat(options.currentScale.toFixed(2), ")");
    }

    requestAnimationFrame(repeatOften);
  } // pointer init


  function pointerInit() {
    if (mouseHighlighter === null) {
      return false;
    } // if mouse highlighter isn't enabled then return false


    if (checkActionStatus(document.querySelector('[data-type="mouse-highlighter"]')) === false) {
      return false;
    }

    requestAnimationFrame(repeatOften);
    cursorPointer.style.display = 'block';
    cursorPointerInner.style.display = 'block';
    cursorPointerInner.style.setProperty('--scale', 1);
    return true;
  } // pointer dispear


  function pointerDisapear() {
    if (supportsCssVariables) {
      cursorPointerInner.style.setProperty('--scale', 0);
    } else {
      cursorPointerInner.style.transform = 'scale(0)';
    }

    bodyRoot.removeEventListener('mousemove', pointerInit);
  } // **** END:: Mouse highligter  **** //
  // **** START:: Content Scalling **** //


  var increment = document.querySelector('#increment');
  var decrement = document.querySelector('#decrement');
  var fontPercent = document.querySelector('.font-percent-value');
  var scalingEnabled = 0;
  window.fontPercent = 0;
  var validTags = ['p', 'div', 'span', 'i', 'b', 'strong', 'section', 'article', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'tr', 'th', 'td', 'a', 'ul', 'ol', 'li', 'input', 'button'];
  validTags = validTags.map(function (element) {
    return "".concat(element, ":not(.font-unresizable)");
  });

  var elements = _toConsumableArray(document.querySelectorAll(validTags.join(', '))); // set original font size on elements


  function setOriginFontSizeAttr() {
    elements.forEach(function (el) {
      var fontSize = window.getComputedStyle(el, null).getPropertyValue('font-size');

      if (typeof fontSize !== 'undefined' && fontSize !== '') {
        el.setAttribute('data-original-font-size', fontSize);
      }
    });
  } // reset font size


  function toggleFontSize() {
    var toggleType = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'increase';
    elements.forEach(function (el) {
      var fontSize = window.getComputedStyle(el, null).getPropertyValue('font-size');

      if (typeof fontSize !== 'undefined' && fontSize !== '') {
        fontSize = parseFloat(fontSize);

        if (toggleType === 'reset') {
          el.style.fontSize = null;
          el.removeAttribute('data-original-font-size');
          fontPercent.innerHTML = '0%';
          scalingEnabled = 0; // if style attribs doens't have value then remove it

          if (el.getAttribute('style') === '') {
            el.removeAttribute('style');
          }
        } else if (toggleType === 'decrease') {
          fontSize -= 1;
          el.style.fontSize = "".concat(fontSize, "px");
        } else {
          fontSize += 1;
          el.style.fontSize = "".concat(fontSize, "px");
        }
      }
    });
  }

  increment.addEventListener('click', function (event) {
    event.preventDefault(); // if content scaling class hasn't exist

    if (a11yScalingWrap === null) {
      return false;
    } // if scaling first click then set properties


    if (scalingEnabled === 0) {
      setOriginFontSizeAttr();
      scalingEnabled = 1;
    } // if font percentage is "100%" then return false


    if (window.fontPercent >= 5) {
      return false;
    }

    window.fontPercent += 1;
    fontPercent.innerHTML = "".concat(window.fontPercent * 10, "%");

    if (window.fontPercent === 0) {
      toggleFontSize('reset');
      saveA11ySettings();
      return false;
    }

    toggleFontSize('increase');
    saveA11ySettings();
    return true;
  });
  decrement.addEventListener('click', function (event) {
    event.preventDefault(); // if content scaling class hasn't exist

    if (a11yScalingWrap == null) {
      return false;
    } // if scaling first click then set properties


    if (scalingEnabled === 0) {
      setOriginFontSizeAttr();
      scalingEnabled = 1;
    } // if font percentage is "-100%" then return false


    if (window.fontPercent <= -5) {
      return false;
    }

    window.fontPercent -= 1;
    fontPercent.innerHTML = "".concat(window.fontPercent * 10, "%");

    if (window.fontPercent === 0) {
      toggleFontSize('reset');
      saveA11ySettings();
      return false;
    }

    toggleFontSize('decrease');
    saveA11ySettings();
    return true;
  }); // **** END:: Content scalling **** //
  // change action status

  function changeAcitonStatus() {
    var actionBtn = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    var actionType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

    if (actionType === 'reset') {
      // all action button
      a11yActionBtn.forEach(function (acBtn) {
        if (acBtn.classList.contains('a11y-active')) {
          acBtn.classList.remove('a11y-active');
        }
      });
      Joomla.renderMessages({
        message: ['Successfully reset.']
      });
      return true;
    }

    if (actionStatus === true) {
      Joomla.renderMessages({
        message: ['Successfully applied.']
      });
      actionBtn.classList.toggle('a11y-active');
      return true;
    }

    Joomla.renderMessages({
      warning: ['Something went wrong!']
    });
    return true;
  } // Reset Accessibility


  a11yResetBtn.addEventListener('click', function (e) {
    e.preventDefault(); // all accessibility classes

    var bodyA11yClasses = ['a11y-grayscale', 'a11y-enable-contrast', 'a11y-big-black-cursor', 'a11y-big-white-cursor', 'a11y-no-motion', 'a11y-magnifier', 'a11y-mouse-highlighter']; // remove accessiblity classes from body and html

    bodyA11yClasses.forEach(function (bodyA11yClass) {
      if (bodyRoot.classList.contains(bodyA11yClass)) {
        bodyRoot.classList.remove(bodyA11yClass);
      } else if (htmlRoot.classList.contains(bodyA11yClass)) {
        htmlRoot.classList.remove(bodyA11yClass);
      }
    }); // reset all actions except content scalling and cursor magnifier

    changeAcitonStatus('', 'reset');
    removeMagnifier(); // reset conent scalling

    if (a11yScalingWrap !== null) {
      toggleFontSize('reset');
    } // reset mouse highlighter


    if (mouseHighlighter !== null) {
      pointerDisapear();
    }
    
    // Clear localStorage
    clearA11ySettings();
  });
  
  // Restore accessibility settings from localStorage
  function restoreA11ySettings() {
    var settings = loadA11ySettings();
    if (!settings) {
      return;
    }
    
    // Restore font scaling
    if (settings.fontPercent && settings.fontPercent !== 0 && a11yScalingWrap !== null && fontPercent !== null) {
      if (scalingEnabled === 0) {
        setOriginFontSizeAttr();
        scalingEnabled = 1;
      }
      
      var targetPercent = settings.fontPercent;
      var currentPercent = window.fontPercent || 0;
      var diff = targetPercent - currentPercent;
      
      if (diff !== 0) {
        for (var i = 0; i < Math.abs(diff); i++) {
          if (diff > 0) {
            toggleFontSize('increase');
          } else {
            toggleFontSize('decrease');
          }
        }
        window.fontPercent = targetPercent;
        fontPercent.innerHTML = ''.concat(targetPercent * 10, '%');
      }
    }
    
    // Restore grayscale
    if (settings.grayscale) {
      htmlRoot.classList.add('a11y-grayscale');
      var grayscaleBtn = document.querySelector('[data-type="grayscale"]');
      if (grayscaleBtn) {
        grayscaleBtn.classList.add('a11y-active');
      }
    }
    
    // Restore contrast
    if (settings.contrast) {
      htmlRoot.classList.add('a11y-enable-contrast');
      var contrastBtn = document.querySelector('[data-type="contrast"]');
      if (contrastBtn) {
        contrastBtn.classList.add('a11y-active');
      }
    }
    
    // Restore no motion
    if (settings.nomotion) {
      bodyRoot.classList.add('a11y-no-motion');
      var nomotionBtn = document.querySelector('[data-type="nomotion"]');
      if (nomotionBtn) {
        nomotionBtn.classList.add('a11y-active');
      }
    }
    
    // Restore big black cursor
    if (settings.bbcursor) {
      bodyRoot.classList.add('a11y-big-black-cursor');
      var bbcursorBtn = document.querySelector('[data-type="bbcursor"]');
      if (bbcursorBtn) {
        bbcursorBtn.classList.add('a11y-active');
      }
    }
    
    // Restore big white cursor
    if (settings.bhcursor) {
      bodyRoot.classList.add('a11y-big-white-cursor');
      var bhcursorBtn = document.querySelector('[data-type="bhcursor"]');
      if (bhcursorBtn) {
        bhcursorBtn.classList.add('a11y-active');
      }
    }
    
    // Restore magnifier
    if (settings.magnifier) {
      bodyRoot.classList.add('a11y-magnifier');
      magnifierInit();
      var magnifierBtn = document.querySelector('[data-type="magnifier"]');
      if (magnifierBtn) {
        magnifierBtn.classList.add('a11y-active');
      }
    }
    
    // Restore mouse highlighter
    if (settings.mouseHighlighter) {
      bodyRoot.classList.add('a11y-mouse-highlighter');
      pointerAppear();
      var mouseHighlighterBtn = document.querySelector('[data-type="mouse-highlighter"]');
      if (mouseHighlighterBtn) {
        mouseHighlighterBtn.classList.add('a11y-active');
      }
    }
  }
  
  // Restore settings on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restoreA11ySettings);
  } else {
    restoreA11ySettings();
  }
})(window.Joomla, document);