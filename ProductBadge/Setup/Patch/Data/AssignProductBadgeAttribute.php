<?php

declare(strict_types=1);

namespace Codilar\ProductBadge\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AssignProductBadgeAttribute implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavSetupFactory $eavSetupFactory,
        private AttributeSetFactory $attributeSetFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        $attributeId = $eavSetup->getAttributeId(
            Product::ENTITY,
            'product_badge'
        );

        if (!$attributeId) {
            $this->moduleDataSetup->getConnection()->endSetup();

            return $this;
        }

        $attributeSets = $eavSetup->getAllAttributeSetIds(
            Product::ENTITY
        );

        foreach ($attributeSets as $attributeSetId) {
            $attributeSet = $this->attributeSetFactory->create();

            $attributeSet->load($attributeSetId);

            $groupId = $attributeSet->getDefaultGroupId();

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

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
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
