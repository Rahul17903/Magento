<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Controller\Adminhtml\ProductEnquiry;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Codilar_ProductEnquiry::product_enquiry';

    public function __construct(
        Context $context,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(
                __('Unable to delete the enquiry.')
            );

            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $this->productEnquiryRepository->deleteById($id);

            $this->messageManager->addSuccessMessage(
                __('Product enquiry has been deleted successfully.')
            );
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting the enquiry.')
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
