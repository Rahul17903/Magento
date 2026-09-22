define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var $account = $(element);
        var $trigger = $account.find('.header-account-trigger');

        $trigger.on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var isOpen = $account.hasClass('is-open');

            $('.header-account').removeClass('is-open');
            $('.header-account-trigger').attr('aria-expanded', 'false');

            if (!isOpen) {
                $account.addClass('is-open');
                $trigger.attr('aria-expanded', 'true');
            }
        });

        $(document).on('click.headerAccount', function () {
            $account.removeClass('is-open');
            $trigger.attr('aria-expanded', 'false');
        });

        $account.on('click', function (event) {
            event.stopPropagation();
        });
    };
});
