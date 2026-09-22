<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Block;

use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry\Collection;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry\CollectionFactory;
use Magento\Framework\View\Element\Template;

class EnquiryCollection extends Template
{
    private Collection $collection;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->collection = $collectionFactory->create();
    }

    public function getEnquiries(): Collection
    {
        return $this->collection;
    }
}
