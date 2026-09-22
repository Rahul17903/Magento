<?php

declare(strict_types=1);

namespace Codilar\HomeCampaign\Block\Widget;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;

class Campaign extends Template implements BlockInterface
{
    private const DEFAULT_PRODUCT_LIMIT = 8;

    public function __construct(
        Context $context,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly Visibility $visibility,
        private readonly ImageHelper $imageHelper,
        private readonly StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Set widget template.
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->setTemplate(
            'Codilar_HomeCampaign::widget/campaign.phtml'
        );
    }

    /**
     * Get campaign title.
     */
    public function getCampaignTitle(): string
    {
        return (string)($this->getData('campaign_title')
            ?: __('Campaign 1 Banner'));
    }

    /**
     * Get campaign image.
     */
    public function getCampaignImage(): string
    {
        return (string)$this->getData('campaign_image');
    }

    /**
     * Get category ID.
     */
    public function getCategoryId(): int
    {
        return (int)$this->getData('category_id');
    }

    /**
     * Get product limit.
     */
    public function getProductLimit(): int
    {
        $limit = (int)$this->getData('product_limit');

        return $limit > 0
            ? $limit
            : self::DEFAULT_PRODUCT_LIMIT;
    }

    /**
     * Get button text.
     */
    public function getButtonText(): string
    {
        return (string)($this->getData('button_text')
            ?: __('Browse All Products'));
    }

    /**
     * Get button URL.
     */
    public function getButtonUrl(): string
    {
        $url = (string)$this->getData('button_url');

        if (!$url) {
            return '#';
        }

        return $this->getUrl($url);
    }

    /**
     * Get products from selected category.
     */
    public function getProducts(): Collection
    {
        try {
            $categoryId = $this->getCategoryId();

            if (!$categoryId) {
                return $this->productCollectionFactory->create();
            }

            $storeId = (int)$this->storeManager->getStore()->getId();

            $collection = $this->productCollectionFactory->create();

            $collection->setStoreId($storeId);

            $collection->addAttributeToSelect([
                'name',
                'sku',
                'price',
                'special_price',
                'special_from_date',
                'special_to_date',
                'small_image',
                'thumbnail',
                'status',
                'visibility'
            ]);

            $collection->addCategoriesFilter([
                'in' => [$categoryId]
            ]);

            $collection->addAttributeToFilter(
                'status',
                Status::STATUS_ENABLED
            );

            $collection->setVisibility(
                $this->visibility->getVisibleInSiteIds()
            );

            $collection->addStoreFilter($storeId);

            $collection->setOrder(
                'position',
                'ASC'
            );

            $collection->setPageSize(
                $this->getProductLimit()
            );

            return $collection;
        } catch (\Throwable $e) {
            return $this->productCollectionFactory->create();
        }
    }

    /**
     * Get product image URL.
     */
    public function getProductImageUrl(
        \Magento\Catalog\Model\Product $product
    ): string {
        try {
            return $this->imageHelper
                ->init($product, 'category_page_grid')
                ->getUrl();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Get product URL.
     */
    public function getProductUrl(
        \Magento\Catalog\Model\Product $product
    ): string {
        try {
            return $product->getProductUrl();
        } catch (\Throwable $e) {
            return '#';
        }
    }

    /**
     * Check whether product is saleable.
     */
    public function isSaleable(
        \Magento\Catalog\Model\Product $product
    ): bool {
        try {
            return $product->isSaleable();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get add to cart URL.
     */
    public function getAddToCartUrl(
        \Magento\Catalog\Model\Product $product
    ): string {
        try {
            return $this->getUrl(
                'checkout/cart/add',
                [
                    'product' => (int)$product->getId()
                ]
            );
        } catch (\Throwable $e) {
            return '#';
        }
    }

    /**
     * Get wishlist URL.
     */
    public function getWishlistUrl(
        \Magento\Catalog\Model\Product $product
    ): string {
        try {
            return $this->getUrl(
                'wishlist/index/index/',
                [
                    'wishlist_id' => (int)$product->getId()
                ]
            );
        } catch (\Throwable $e) {
            return '#';
        }
    }
}
