<?php
/**
 * This file tells Magento "hey, a module named Codilar_ProductBadge lives here".
 * Every Magento module needs this file.
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Codilar_ProductBadge',
    __DIR__
);
