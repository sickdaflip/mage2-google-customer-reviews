<?php

declare(strict_types=1);

namespace FlipDev\GoogleCustomerReviews\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Removes all module configuration entries on uninstall.
 *
 * Run via: bin/magento module:uninstall FlipDev_GoogleCustomerReviews
 */
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->delete(
            $setup->getTable('core_config_data'),
            [$connection->quoteInto('path LIKE ?', 'flipdev_gcr/%')]
        );

        $setup->endSetup();
    }
}
