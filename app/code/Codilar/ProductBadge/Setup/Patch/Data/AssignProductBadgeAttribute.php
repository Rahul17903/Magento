<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AssignProductBadgeAttribute implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        try {
            $eavSetup = $this->eavSetupFactory->create([
                'setup' => $this->moduleDataSetup
            ]);

            $attributeId = $eavSetup->getAttributeId(
                Product::ENTITY,
                'product_badge'
            );

            if (!$attributeId) {
                return $this;
            }

            $attributeSetIds = $eavSetup->getAllAttributeSetIds(
                Product::ENTITY
            );

            foreach ($attributeSetIds as $attributeSetId) {
                $groupId = $eavSetup->getDefaultAttributeGroupId(
                    Product::ENTITY,
                    $attributeSetId
                );

                if (!$groupId) {
                    continue;
                }

                $eavSetup->addAttributeToGroup(
                    Product::ENTITY,
                    $attributeSetId,
                    $groupId,
                    $attributeId
                );
            }

            return $this;
        } finally {
            $connection->endSetup();
        }
    }

    public static function getDependencies(): array
    {
        return [
            AddProductBadgeAttribute::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}
