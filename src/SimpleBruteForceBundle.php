<?php

namespace EvozonPhp\SimpleBruteForceBundle;

use EvozonPhp\SimpleBruteForceBundle\DependencyInjection\Compiler\AccessDecisionManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Simple Brute-Force Bundle.
 *
 * @author Constantin Bejenaru <constantin.bejenaru@evozon.com>
 */
class SimpleBruteForceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new AccessDecisionManagerPass());
    }
}
