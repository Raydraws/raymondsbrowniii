(() => {
    'use strict';

    jQuery(document).ready(function ($) {
        const bxSliderSelector = '.sppb-dc-collection-bxslider';
        const observeAttributes = [
            'data-autoplay', 'data-pause-on-hover', 'data-loop',
            'data-interval', 'data-speed', 'data-controllers',
            'data-arrows', 'data-arrow-content', 'data-carousel-items',
        ];
        let sliderInstance = {};
        let initialLoad = {};

        function readSettings($slider) {
            return {
                autoplay: $slider.attr('data-autoplay') === 'true',
                pauseOnHover: $slider.attr('data-pause-on-hover') === 'true',
                loop: $slider.attr('data-loop') === 'true',
                interval: parseInt($slider.attr('data-interval'), 10) || 5000,
                speed: parseInt($slider.attr('data-speed'), 10) || 600,
                controllers: $slider.attr('data-controllers') === 'true',
                arrows: $slider.attr('data-arrows') === 'true',
                arrowContent: $slider.attr('data-arrow-content') || 'icon_only',
                carouselItems: parseInt($slider.attr('data-carousel-items'), 10) || 1,
            };
        }

        function arrowMarkup(direction, arrowContent) {
            const icon = direction === 'next'
                ? `<i class="fa fa-angle-right" aria-hidden="true"></i>`
                : `<i class="fa fa-angle-left" aria-hidden="true"></i>`;
            const longIcon = direction === 'next'
                ? `<i class="fa fa-long-arrow-right" aria-hidden="true"></i>`
                : `<i class="fa fa-long-arrow-left" aria-hidden="true"></i>`;
            const text = direction === 'next' ? 'Next' : 'Prev';

            switch (arrowContent) {
                case 'text_only':
                    return text;
                case 'long_arrow':
                    return longIcon;
                case 'icon_with_text':
                    return `${icon} ${text}`;
                case 'icon_only':
                default:
                    return icon;
            }
        }

        function initSlider($slider, addonId) {
            const settings = readSettings($slider);

            sliderInstance[addonId] = $slider.bxSlider({
                minSlides: settings.carouselItems,
                maxSlides: settings.carouselItems,
                slideWidth: 1140,
                infiniteLoop: settings.loop,
                controls: settings.arrows,
                pager: settings.controllers,
                auto: settings.autoplay,
                pause: settings.interval,
                autoHover: settings.pauseOnHover,
                speed: settings.speed,
                nextText: arrowMarkup('next', settings.arrowContent),
                prevText: arrowMarkup('prev', settings.arrowContent),
            });
        }

        function reinitSlider($slider, addonId) {
            if (!addonId) return;
            if (sliderInstance[addonId] && typeof sliderInstance[addonId].destroySlider === 'function') {
                sliderInstance[addonId].destroySlider();
            }
            delete sliderInstance[addonId];
            initialLoad[addonId] = false;
            initSlider($slider, addonId);
            
        }


        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                var newNodes = mutation.addedNodes;
                if (newNodes !== null) {
                    $(newNodes).each(function () {
                        var $node = $(this);
                        if ($node.find(bxSliderSelector)) {
                            $node.find(bxSliderSelector).each(function () {
                                const $slider = $(this);
                                const addonId = $slider.attr('data-addon-id');

                                if (!initialLoad[addonId] && !sliderInstance[addonId]) {
                                    jQuery(function () {
                                        initSlider($slider, addonId);
                                    });
                                    initialLoad[addonId] = true;
                                }
                            });
                        }
                    });
                }
            });
        });

        var attributesObserver = new MutationObserver(function (mutations) {
            const processedAddons = new Set();
            for (const mutation of mutations) {
                if (
                    mutation.type === 'attributes' &&
                    observeAttributes.includes(mutation.attributeName)
                ) {
                    const $slider = $(mutation.target);
                    const addonId = $slider.attr('data-addon-id');

                    if (!addonId || processedAddons.has(addonId)) continue;
                    processedAddons.add(addonId);

                    reinitSlider($slider, addonId);
                }
            }
        });

        var config = {
            childList: true,
            subtree: true,
            attributes: true,
        };

        var attributesConfig = {
            attributes: true,
            subtree: true,
            attributeOldValue: true,
        };

        observer.observe(document.body, config);
        attributesObserver.observe(document.body, attributesConfig);

        document.addEventListener('sppb:dc:updated', function (e) {
            const $slider = jQuery(e.detail.root).find(bxSliderSelector);
            if (!$slider.length) return;
            const addonId = $slider.attr('data-addon-id');
            reinitSlider($slider, addonId);
            
        });

        // MutationObserver only fires for nodes added after observe() starts, so it
        // never catches sliders already present in the server-rendered initial HTML
        // (a normal page load/hard refresh). Init those directly here.
        $(bxSliderSelector).each(function () {
            const $slider = $(this);
            const addonId = $slider.attr('data-addon-id');

            if (!initialLoad[addonId] && !sliderInstance[addonId]) {
                initSlider($slider, addonId);
                initialLoad[addonId] = true;
            }
        });
    });
})();
