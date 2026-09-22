define([
    'uiComponent',
    'ko',
    'Magento_Customer/js/customer-data'
], function (Component, ko, customerData) {
    'use strict';

    return Component.extend({

        defaults: {
            template: 'Codilar_CartProgress/minicart/progress'
        },

        milestones: [
            {
                amount: 30,
                label: 'Free Delivery',
                subLabel: 'Auto applied',
                type: 'delivery'
            },
            {
                amount: 100,
                label: '$50 OFF',
                subLabel: 'View',
                type: 'coupon'
            },
            {
                amount: 200,
                label: '$100 OFF',
                subLabel: 'Coupon',
                type: 'coupon'
            },
            {
                amount: 300,
                label: '$150 OFF',
                subLabel: 'Coupon',
                type: 'coupon'
            }
        ],

        cart: null,

        initialize: function () {
            this._super();

            this.cart = customerData.get('cart');

            return this;
        },

        getSubtotal: function () {
            var cartData = this.cart();

            return parseFloat(cartData.subtotalAmount) || 0;
        },

        getNextMilestone: function () {
            var subtotal = this.getSubtotal();

            for (var i = 0; i < this.milestones.length; i++) {
                if (subtotal < this.milestones[i].amount) {
                    return this.milestones[i];
                }
            }

            return null;
        },

        getAmountRemaining: function () {
            var nextMilestone = this.getNextMilestone();

            if (!nextMilestone) {
                return 0;
            }

            return Math.max(
                0,
                nextMilestone.amount - this.getSubtotal()
            );
        },

        isAllUnlocked: function () {
            return this.getNextMilestone() === null;
        },

        isCompleted: function (milestone) {
            return this.getSubtotal() >= milestone.amount;
        },

        getSegmentFill: function (milestone, index) {
            var subtotal = this.getSubtotal();

            var from = index === 0
                ? 0
                : this.milestones[index - 1].amount;

            var to = milestone.amount;

            if (subtotal >= to) {
                return 100;
            }

            if (subtotal <= from) {
                return 0;
            }

            return ((subtotal - from) / (to - from)) * 100;
        },

        formatAmount: function (amount) {
            return '$' + amount.toFixed(2);
        },

        getHeading: function () {
            var nextMilestone = this.getNextMilestone();

            if (!nextMilestone) {
                return 'All rewards unlocked!';
            }

            return 'Shop ' +
                this.formatAmount(this.getAmountRemaining()) +
                ' more, Unlock ' +
                nextMilestone.label;
        }
    });
});
