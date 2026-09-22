define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var $section = $(element),
            $slider = $section.find('.home-campaign-slider'),
            $track = $section.find('.home-campaign-track'),
            $slides = $section.find('.home-campaign-slide'),
            $next = $section.find('.home-campaign-next'),
            $prev = $section.find('.home-campaign-prev'),
            currentIndex = 0;

        if (!$slider.length || !$track.length || !$slides.length) {
            return;
        }

        function getVisibleSlides() {
            var width = window.innerWidth;

            if (width <= 575) {
                return 1;
            }

            if (width <= 767) {
                return 2;
            }

            if (width <= 1199) {
                return 3;
            }

            return 4;
        }

        function getSlideWidth() {
            return $slider.innerWidth() / getVisibleSlides();
        }

        function getMaxIndex() {
            return Math.max(
                0,
                $slides.length - getVisibleSlides()
            );
        }

        function updateSlider(animate) {
            var slideWidth = getSlideWidth(),
                translateX = currentIndex * slideWidth;

            $track.css(
                'transition',
                animate
                    ? 'transform .35s ease'
                    : 'none'
            );

            $track.css(
                'transform',
                'translate3d(-' + translateX + 'px, 0, 0)'
            );

            updateButtons();
        }

        function updateButtons() {
            var maxIndex = getMaxIndex();

            $prev.prop(
                'disabled',
                currentIndex <= 0
            );

            $next.prop(
                'disabled',
                currentIndex >= maxIndex
            );
        }

        $next.on('click', function () {
            var maxIndex = getMaxIndex();

            if (currentIndex < maxIndex) {
                currentIndex++;
                updateSlider(true);
            }
        });

        $prev.on('click', function () {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider(true);
            }
        });

        $(window).on(
            'resize.homeCampaign',
            function () {
                var maxIndex = getMaxIndex();

                if (currentIndex > maxIndex) {
                    currentIndex = maxIndex;
                }

                updateSlider(false);
            }
        );

        updateSlider(false);
    };
});
