<?php

namespace Pim\Bundle\VersioningBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PimVersioningExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('guessers.yml');
        $loader->load('managers.yml');
        $loader->load('builders.yml');
        $loader->load('event_listeners.yml');

        $storageDriver = $container->getParameter('pim_catalog.storage_driver');
        $storageConfig = sprintf('storage_driver/%s.yml', $storageDriver);
        if (file_exists(__DIR__ . '/../Resources/config/' . $storageConfig)) {
            $loader->load($storageConfig);
        }

        $file = __DIR__.'/../Resources/config/pim_versioning_entities.yml';
        $entities = Yaml::parse(realpath($file));
        $container->setParameter('pim_versioning.versionable_entities', $entities['versionable']);
    }
}
