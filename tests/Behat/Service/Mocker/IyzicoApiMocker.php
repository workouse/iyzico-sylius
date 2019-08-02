<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Service\Mocker;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class IyzicoApiMocker
{
    /** @var MockerInterface */
    private $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockApiSuccessfulVerifyTransaction(callable $action): void
    {
        $mockService = $this->mocker
            ->mockService('eres_sylius_iyzico_plugin.bridge.iyzico', IyzicoBridgeInterface::class);

        $mockService->shouldReceive('setAuthorizationData');
        $mockService->shouldReceive('create')->andReturn(true);

        $action();

        $this->mocker->unmockService('eres_sylius_iyzico_plugin.bridge.iyzico');
    }
}
