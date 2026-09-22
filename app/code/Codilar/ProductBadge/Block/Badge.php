<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;

class Badge extends Template
{
    public function getBadge(Product $product): ?string
    {
        $badge = $product->getAttributeText('product_badge');

        if (!$badge || $badge === 'No Badge') {
            return null;
        }

        return (string) $badge;
    }

    public function getBadgeClass(string $badge): string
    {
        return match ($badge) {
            'New' => 'new-badge',
            'Sale' => 'sale-badge',
            'Out of Stock' => 'stock-badge',
            default => '',
        };
    }
}
