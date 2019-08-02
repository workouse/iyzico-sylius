<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function setEnvironment(string $environment): void;

    public function setApiKey(string $apiKey): void;

    public function setSecretKey(string $secretKey): void;

    public function setThreeds($threeds): void;
}
