<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProductBadgeAttribute implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_badge',
            [
                'type' => 'int',
                'label' => 'Product Badge',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'required' => false,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'visible_on_front' => false,
                'searchable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'comparable' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'sort_order' => 100,
                'option' => [
                    'values' => [
                        'No Badge',
                        'New',
                        'Sale',
                        'Out of Stock',
                    ],
                ],
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
