<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Block;

use Magento\Catalog\Block\Product\View;

class ProductBadge extends View
{
    public function getBadge(): ?string
    {
        try {
            $product = $this->getProduct();

            if (!$product) {
                return null;
            }

            $badge = $product->getAttributeText('product_badge');

            if (!$badge || $badge === 'No Badge') {
                return null;
            }

            return (string) $badge;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getBadgeClass(string $badge): string
    {
        try {
            return match ($badge) {
                'New' => 'new-badge',
                'Sale' => 'sale-badge',
                'Out of Stock' => 'stock-badge',
                default => '',
            };
        } catch (\Throwable $e) {
            return '';
        }
    }
}
