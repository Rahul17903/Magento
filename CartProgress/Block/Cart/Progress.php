<?php
declare(strict_types=1);

namespace Codilar\CartProgress\Block\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Progress extends Template
{
    private const MILESTONES = [
        [
            'amount'    => 30,
            'label'     => 'Free Delivery',
            'sub_label' => 'Auto applied',
            'type'      => 'delivery',
        ],
        [
            'amount'    => 100,
            'label'     => '$50 OFF',
            'sub_label' => 'View',
            'type'      => 'coupon',
        ],
        [
            'amount'    => 200,
            'label'     => '$100 OFF',
            'sub_label' => 'Coupon',
            'type'      => 'coupon',
        ],
        [
            'amount'    => 300,
            'label'     => '$150 OFF',
            'sub_label' => 'Coupon',
            'type'      => 'coupon',
        ],
    ];

    public function __construct(
        Context $context,
        private CheckoutSession $checkoutSession,
        private PricingHelper $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Current cart subtotal, read live from the active quote.
     */
    public function getCartSubtotal(): float
    {
        $quote = $this->checkoutSession->getQuote();

        return (float) $quote->getSubtotal();
    }

    /**
     * @return array
     */
    public function getMilestones(): array
    {
        $subtotal = $this->getCartSubtotal();
        $milestones = [];
        $previousAmount = 0.0;

        foreach (array_values(self::MILESTONES) as $index => $milestone) {
            $milestones[] = array_merge($milestone, [
                'index'        => $index,
                'is_completed' => $subtotal >= $milestone['amount'],
                'segment_fill' => $this->getSegmentFillPercent($subtotal, $previousAmount, $milestone['amount']),
            ]);

            $previousAmount = $milestone['amount'];
        }

        return $milestones;
    }

    /**
     * @param float $subtotal
     * @param float $from
     * @param float $to
     * @return float
     */
    private function getSegmentFillPercent(float $subtotal, float $from, float $to): float
    {
        if ($subtotal >= $to) {
            return 100.0;
        }

        if ($subtotal <= $from) {
            return 0.0;
        }

        $range = $to - $from;
        if ($range <= 0.0) {
            return 100.0;
        }

        return round((($subtotal - $from) / $range) * 100, 2);
    }

    /**
     * @return array|null
     */
    public function getNextMilestone(): ?array
    {
        $subtotal = $this->getCartSubtotal();

        foreach (self::MILESTONES as $milestone) {
            if ($subtotal < $milestone['amount']) {
                return $milestone;
            }
        }

        return null;
    }

    /**
     * Amount still needed to reach the next milestone.
     */
    public function getAmountRemaining(): float
    {
        $next = $this->getNextMilestone();
        if ($next === null) {
            return 0.0;
        }

        return round($next['amount'] - $this->getCartSubtotal(), 2);
    }

    /**
     * True once the customer has unlocked every configured milestone.
     */
    public function getAllMilestonesUnlocked(): bool
    {
        return $this->getNextMilestone() === null;
    }

    /**
     * @param float $amount
     * @return string
     */
    public function formatMilestoneAmount(float $amount): string
    {
        return $this->pricingHelper->currency($amount, true, false);
    }
}
