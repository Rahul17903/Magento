define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/get-totals'
], function ($, customerData, getTotalsAction) {
    'use strict';

    $(document).on(
        'click.cartQty',
        '.qty-plus, .qty-minus',
        function (event) {

            event.preventDefault();
            event.stopImmediatePropagation();

            var button = $(this);

            var input = button
                .closest('.field.qty')
                .find('input.qty');

            if (!input.length) {
                return false;
            }

            var currentQty = parseFloat(input.val()) || 1;
            var newQty = currentQty;

            if (button.hasClass('qty-plus')) {
                newQty = currentQty + 1;
            }

            if (button.hasClass('qty-minus')) {
                newQty = Math.max(1, currentQty - 1);
            }

            if (newQty === currentQty) {
                return false;
            }

            var form = $('#form-validate');

            if (!form.length) {
                console.error('Cart form not found.');
                return false;
            }

            var oldQty = currentQty;

            input.val(newQty);

            $('.qty-plus, .qty-minus')
                .prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                showLoader: true,

                success: function (response) {

                    customerData.reload(['cart'], true);

                    var deferred = $.Deferred();

                    getTotalsAction([], deferred);

                    var parsedResponse = $($.parseHTML(response));

                    var updatedProgressBar = parsedResponse.find('.cart-summary > .cart-progress-bar');

                    var currentProgressBar = $('.cart-summary > .cart-progress-bar');

                    if (updatedProgressBar.length && currentProgressBar.length) {
                        currentProgressBar.replaceWith(updatedProgressBar);
                    }

                    var itemName = input.attr('name');

                    if (itemName) {

                        var updatedInput = parsedResponse.find('input[name="' + itemName + '"]');

                        if (updatedInput.length) {

                            var updatedRow = updatedInput.closest('tbody.cart.item.card-item-row');

                            var currentRow = input.closest('tbody.cart.item.card-item-row');

                            if (updatedRow.length && currentRow.length) {
                                var updatedSubtotal = updatedRow.find('.col.subtotal');

                                var currentSubtotal = currentRow.find('.col.subtotal');

                                if (updatedSubtotal.length && currentSubtotal.length) {

                                    currentSubtotal.html(updatedSubtotal.html());
                                }
                            }

                            input.val(
                                updatedInput.val()
                            );
                        }
                    }
                },

                error: function (xhr) {

                    input.val(oldQty);

                    console.error(
                        'Unable to update cart quantity.',
                        xhr.status,
                        xhr.responseText
                    );
                },

                complete: function () {

                    $('.qty-plus, .qty-minus')
                        .prop('disabled', false);
                }
            });

            return false;
        }
    );
});
