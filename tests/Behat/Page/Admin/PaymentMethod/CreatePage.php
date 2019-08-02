<?php


namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{

    public function setEnvironment(string $environment): void
    {
        $this->getDocument()->selectFieldOption('Environment', $environment);
    }

    public function setApiKey(string $apiKey): void
    {
        $this->getDocument()->fillField('Api key', $apiKey);
    }

    public function setSecretKey(string $secretKey): void
    {
        $this->getDocument()->fillField('Secret key', $secretKey);
    }

    public function setThreeds($threeds): void
    {
        $this->getDocument()->selectFieldOption('3D Auth', $threeds);
    }
}
