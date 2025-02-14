<?php
/**
 * ITCS Fields File - based on Media Manger
 * ------------------------------------------------------------------------
 * @package     ITCS.Plugin
 * @subpackage  Fields.file
 *
 * @author      it-conserv.de
 * @copyright   Copyright (C) 2025 it-conserv.de All Rights Reserved.
 * @license     License - GNU/GPLv3 <http://www.gnu.org/licenses/gpl-3.0.de.html>
 * @link        it-conserv.de
 * ------------------------------------------------------------------------
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use ITCS\Plugin\Fields\File\Extension\File;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin     = new File(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('fields', 'file')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
