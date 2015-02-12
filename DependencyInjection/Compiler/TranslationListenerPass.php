<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $driver = $container->getParameter('sylius_translation.driver');

        if ($driver == SyliusResourceBundle::DRIVER_DOCTRINE_ORM) {
            if (!$container->hasDefinition('sylius.translatable.listener')) {
                return;
            }

            // Add high priority to translatable listener
            $translatableListener = $container->getDefinition(
                'sylius.translatable.listener'
            );

            /**
             * TODO make sure this subscriber has higher priority than LoadORMMetadataSubscriber
             * It is absolutely necessary to have the translation metadata loaded before sylius resolves entity metadata
             */
            $translatableListener->addTag('doctrine.event_subscriber', array('priority' => 99));
        } elseif ($driver == SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM) {
            if (!$container->hasDefinition('sylius.odm_translatable.listener')) {
                return;
            }

            // Add high priority to translatable listener
            $translatableListener = $container->getDefinition(
                'sylius.odm_translatable.listener'
            );

            /**
             * TODO make sure this subscriber has higher priority than LoadORMMetadataSubscriber
             * It is absolutely necessary to have the translation metadata loaded before sylius resolves entity metadata
             */
            $translatableListener->addTag('doctrine_mongodb.odm.event_subscriber', array('priority' => 99));
        }
    }
}
