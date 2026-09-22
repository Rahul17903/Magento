<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Plugin;

use Codilar\ProductBadge\Block\Badge;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;

class ProductListPlugin
{
    public function afterGetProductDetailsHtml(
        ListProduct $subject,
        string $result,
        Product $product
    ): string {
        $badgeBlock = $subject->getLayout()->createBlock(Badge::class);

        $badgeBlock->setProduct($product);
        $badgeBlock->setTemplate('Codilar_ProductBadge::badge.phtml');

        return $badgeBlock->toHtml() . $result;
    }
}
