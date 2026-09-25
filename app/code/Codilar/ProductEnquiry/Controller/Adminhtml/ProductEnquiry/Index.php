<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Controller\Adminhtml\ProductEnquiry;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface{
    public const ADMIN_RESOURCE = 'Codilar_ProductEnquiry::product_enquiry';

    public function __construct(
        private readonly PageFactory $resultPageFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu(
            self::ADMIN_RESOURCE
        );

        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Product Enquiries'));

        return $resultPage;
    }
}