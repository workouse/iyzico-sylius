<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Bridge;


interface IyzicoBridgeInterface
{
    const SANDBOX_ENVIRONMENT = 'sandbox';
    const PRODUCTION_ENVIRONMENT = 'production';
    const SANDBOX_HOST = 'https://sandbox-api.iyzipay.com';
    const PRODUCTION_HOST = 'https://api.iyzipay.com';
    const FAILED_STATUS = 'failure';
    const COMPLETED_STATUS = 'success';
    const SHIPPING_CATEGORY = 'Shipping';

    public function setAuthorizationData(
        string $apiKey,
        string $secretKey,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void;

    public function create($data);

    public function createThreeds($data);

    public function getHostForEnvironment(): string;

}
