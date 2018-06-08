<?php

namespace EvozonPhp\SimpleBruteForceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SimpleBruteForce Extension.
 *
 * @copyright Evozon Systems SRL (http://www.evozon.com/)
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class SimpleBruteForceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config');

        // load services config
        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('services.yml');

        // configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('simple_brute_force.configuration', $config);
    }
}
