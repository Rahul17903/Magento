<?php
/**
 * Codilar_CartProgress
 *
 * Registers this module with the Magento component system so Magento
 * knows it exists and where to find it.
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Codilar_CartProgress',
    __DIR__
);
