<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Controller\Adminhtml\ProductEnquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry\CollectionFactory;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry as ProductEnquiryResourceModel;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'Codilar_ProductEnquiry::product_enquiry';

    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly ProductEnquiryResourceModel $resourceModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection(
                $this->collectionFactory->create()
            );

            $deletedCount = 0;

            foreach ($collection as $enquiry) {
                $this->resourceModel->delete($enquiry);
                $deletedCount++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 enquiry(s) have been deleted.', $deletedCount)
            );
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting the selected enquiries.')
            );
        }

        return $this->_redirect('*/*/index');
    }
}
