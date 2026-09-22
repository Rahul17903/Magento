define([
    'jquery'
], function ($) {
    'use strict';

    if (window.innerWidth > 768) {
        return;
    }

    var pagination = $('.custom-pagination');
    var productList = $('.products-grid .product-items');

    if (!pagination.length || !productList.length) {
        return;
    }

    var loading = false;

    var currentPage = parseInt(
        pagination.find('.pagination-page.current').text(),
        10
    ) || 1;

    function loadNextPage() {

        if (loading) {
            return;
        }

        var nextLink = pagination.find('.pagination-next');

        if (!nextLink.length || nextLink.hasClass('disabled')) {
            return;
        }

        var nextUrl = nextLink.attr('href');

        if (!nextUrl) {
            return;
        }

        loading = true;

        $.ajax({
            url: nextUrl,
            type: 'GET',
            dataType: 'html',

            success: function (response) {

                var html = $('<div>').append($.parseHTML(response));

                var products = html.find(
                    '.products-grid .product-items > .product-item'
                );

                if (products.length) {

                    productList.append(products);

                    currentPage++;

                    var newPagination = html.find(
                        '.custom-pagination'
                    );

                    if (newPagination.length) {
                        pagination.replaceWith(newPagination);
                        pagination = newPagination;
                    }
                }
            },

            error: function () {
                console.error(
                    'Unable to load next product page.'
                );
            },

            complete: function () {
                loading = false;
            }
        });
    }

    $(window).on(
        'scroll.infiniteScroll',
        function () {

            var scrollTop = $(window).scrollTop();

            var windowHeight = $(window).height();

            var documentHeight = $(document).height();

            if (
                scrollTop + windowHeight >=
                documentHeight - 500
            ) {
                loadNextPage();
            }
        }
    );
});