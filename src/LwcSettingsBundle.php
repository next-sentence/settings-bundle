<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle;

use Lwc\SettingsBundle\DependencyInjection\InstantiateSettingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class LwcSettingsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new InstantiateSettingsPass());
    }
}
