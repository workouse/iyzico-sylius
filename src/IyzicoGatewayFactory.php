<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class IyzicoGatewayFactory extends GatewayFactory
{
    public const FACTORY_NAME = 'iyzico';

    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::FACTORY_NAME,
            'payum.factory_title' => 'Iyzico'
        ]);

        if (false === (bool)$config['payum.api']) {
            $config['payum.default_options'] = [
                'api_key' => null,
                'secret_key' => null,
                'environment' => IyzicoBridgeInterface::SANDBOX_ENVIRONMENT,
                'threeds' => false,
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'api_key',
                'secret_key',
            ];

            $config['payum.api'] = static function (ArrayObject $config): array {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'api_key' => $config['api_key'],
                    'secret_key' => $config['secret_key'],
                    'environment' => $config['environment'],
                    'threeds' => $config['threeds'],
                ];
            };
        }
    }
}
