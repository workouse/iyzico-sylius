<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin;

use Eres\SyliusIyzicoPlugin\DependencyInjection\EresSyliusIyzicoPluginExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class EresSyliusIyzicoPlugin extends Bundle
{
    use SyliusPluginTrait;

    protected function getContainerExtensionClass(): string
    {
        return EresSyliusIyzicoPluginExtension::class;
    }

}
