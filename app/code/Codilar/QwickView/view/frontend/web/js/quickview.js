define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function (config) {

        var popup = $('#quick-view-popup');

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Quick View',
            buttons: []
        };

        modal(options, popup);

        /**
         * Add Quick View button to product image
         */
        $('.product-item').each(function () {

            var productItem = $(this);

            /*
             * Prevent duplicate Quick View button
             */
            if (productItem.find('.quick-view-button').length) {
                return;
            }

            /*
             * Get product URL
             */
            var productUrl = productItem
                .find('.product-item-link')
                .attr('href');

            if (!productUrl) {
                return;
            }

            /*
             * Get product ID from Add to Cart form
             */
            var productId = productItem
                .find('input[name="product"]')
                .first()
                .val();

            if (!productId) {
                return;
            }

            /*
             * Create Quick View icon button
             */
            var quickViewButton = $(
                '<button type="button" ' +
                'class="quick-view-button" ' +
                'title="Quick View" ' +
                'aria-label="Quick View">' +
                '<span class="quick-view-icon"></span>' +
                '</button>'
            );

            quickViewButton.attr(
                'data-product-id',
                productId
            );

            /*
             * Add button inside product image area
             */
            var imageArea = productItem.find(
                '.product-card-image'
            );

            if (imageArea.length) {
                imageArea.append(quickViewButton);
            }
        });

        /**
         * Quick View button click
         */
        $(document).on(
            'click',
            '.quick-view-button',
            function () {

                var productId = $(this).data('product-id');

                if (!productId) {
                    return;
                }

                /*
                 * Show loader
                 */
                $('.quick-view-loader')
                    .text('Loading...')
                    .show();

                /*
                 * Hide previous product data
                 */
                $('.quick-view-product').hide();

                /*
                 * Open modal
                 */
                popup.modal('openModal');

                /*
                 * Load product data
                 */
                $.ajax({
                    url: config.ajaxUrl,

                    type: 'GET',

                    data: {
                        product_id: productId
                    },

                    dataType: 'json',

                    success: function (response) {

                        if (!response.success) {

                            $('.quick-view-loader')
                                .text(response.message);

                            return;
                        }

                        var product = response.product;

                        /*
                         * Product image
                         */
                        $('#quick-view-product-image')
                            .attr('src', product.image)
                            .attr('alt', product.name);

                        /*
                         * Product name
                         */
                        $('#quick-view-product-name')
                            .text(product.name);

                        /*
                         * Product price
                         */
                        $('#quick-view-product-price')
                            .text(product.price);

                        /*
                         * Product SKU
                         */
                        $('#quick-view-product-sku')
                            .text(product.sku);

                        /*
                         * Product stock
                         */
                        $('#quick-view-product-stock')
                            .text(product.stock_status);

                        /*
                         * Product description
                         */
                        $('#quick-view-product-description')
                            .html(product.description);

                        /*
                         * Hide loader
                         * Show product
                         */
                        $('.quick-view-loader').hide();

                        $('.quick-view-product').show();
                    },

                    error: function () {

                        $('.quick-view-loader')
                            .text(
                                'Something went wrong. Please try again.'
                            );
                    }
                });
            }
        );
    };
});
