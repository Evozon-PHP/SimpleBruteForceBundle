<?php

namespace EvozonPhp\SimpleBruteForceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Access Decision Manager CompilerPass.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class AccessDecisionManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $service = 'simple_brute_force.authorization.access_decision_manager';
        $tag = 'simple_brute_force.security.voter';

        if (!$container->has($service)) {
            return;
        }

        $definition = $container->findDefinition($service);
        $voterIds = array_keys($container->findTaggedServiceIds($tag));

        $voters = [];

        foreach ($voterIds as $id) {
            $voters[] = new Reference($id);
        }

        $definition->setArgument('$voters', $voters);
    }
}
