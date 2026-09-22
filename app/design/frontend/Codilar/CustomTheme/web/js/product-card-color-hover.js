define([
    'jquery',
    'Magento_Swatches/js/swatch-renderer'
], function ($) {
    'use strict';

    var originalImages = {},
        selectedProducts = {},
        imageRequests = {};

    function getRenderer($swatch) {
        var $renderer = $swatch.closest(
            '[data-role^="swatch-option-"]'
        );

        return $renderer.length
            ? $renderer.SwatchRenderer('instance')
            : null;
    }

    function getProductKey($productItem) {
        return $productItem.find('.product-item-info').attr('id');
    }

    function getImage($productItem) {
        return $productItem.find('.product-image-photo');
    }

    function saveOriginalImage($productItem) {
        var key = getProductKey($productItem),
            $image = getImage($productItem);

        if (key && $image.length && !originalImages[key]) {
            originalImages[key] = $image.attr('src');
        }
    }

    function setImage($productItem, image) {
        var $image = getImage($productItem);

        if ($image.length && image && image.img) {
            $image.attr('src', image.img);
        }
    }

    function loadImage(widget, productId, $productItem) {
        var images = widget.options.jsonConfig &&
            widget.options.jsonConfig.images &&
            widget.options.jsonConfig.images[productId];

        if (images && images.length) {
            setImage($productItem, images[0]);
            return;
        }

        if (!widget.options.mediaCallback || imageRequests[productId]) {
            return;
        }

        imageRequests[productId] = true;

        $.ajax({
            url: widget.options.mediaCallback,
            type: 'GET',
            dataType: 'json',
            data: {
                product_id: productId,
                isAjax: true
            },
            success: function (response) {
                if (response && response.medium) {
                    setImage($productItem, {
                        img: response.medium
                    });
                }
            },
            complete: function () {
                delete imageRequests[productId];
            }
        });
    }

    function getHoverProductId(widget, $swatch) {
        var $attribute = $swatch.closest('.swatch-attribute'),
            attributeId = $attribute.data('attribute-id'),
            optionId = $swatch.data('option-id'),
            option = widget.optionsMap[attributeId] && widget.optionsMap[attributeId][optionId];

        if (!option || !option.products.length) {
            return null;
        }

        return widget._getAllowedProductWithMinPrice
            ? widget._getAllowedProductWithMinPrice(option.products)
            : option.products[0];
    }

    function showHoverImage($swatch, $productItem) {
        var widget = getRenderer($swatch),
            productId;

        if (!widget) {
            return;
        }

        productId = getHoverProductId(widget, $swatch);

        if (productId) {
            loadImage(widget, productId, $productItem);
        }
    }

    function syncClickedProduct($swatch, $productItem) {
        var widget = getRenderer($swatch),
            key = getProductKey($productItem),
            attempts = 0,
            interval;

        if (!widget || !key) {
            return;
        }

        interval = setInterval(function () {
            var productId = widget.getProduct();

            attempts++;

            if (productId) {
                selectedProducts[key] = productId;
                loadImage(widget, productId, $productItem);
                clearInterval(interval);
            } else if (attempts >= 15) {
                clearInterval(interval);
            }
        }, 100);
    }

    $(document).on(
        'mouseenter',
        '.product-item .swatch-attribute.color .swatch-option.color',
        function () {
            var $swatch = $(this),
                $productItem = $swatch.closest('.product-item');

            if (!$productItem.length) {
                return;
            }

            saveOriginalImage($productItem);
            showHoverImage($swatch, $productItem);
        }
    );

    $(document).on(
        'click.colorProductImage',
        '.product-item .swatch-attribute.color .swatch-option.color',
        function () {
            var $swatch = $(this),
                $productItem = $swatch.closest('.product-item');

            if (!$productItem.length) {
                return;
            }

            saveOriginalImage($productItem);
            syncClickedProduct($swatch, $productItem);
        }
    );

    $(document).on(
        'mouseleave',
        '.product-item .swatch-attribute.color',
        function () {
            var $attribute = $(this),
                $productItem = $attribute.closest('.product-item'),
                key = getProductKey($productItem),
                $selected = $attribute.find(
                    '.swatch-option.color[aria-checked="true"]'
                ),
                $image = getImage($productItem);

            if (!$productItem.length || !$image.length) {
                return;
            }

            if ($selected.length && selectedProducts[key]) {
                var widget = getRenderer($selected);

                if (widget) {
                    loadImage(
                        widget,
                        selectedProducts[key],
                        $productItem
                    );
                }

                return;
            }

            if (originalImages[key]) {
                $image.attr('src', originalImages[key]);
            }
        }
    );
});
