<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Eres\SyliusIyzicoPlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;

class ManagingPaymentMethodIyzicoContext implements Context
{

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var CreatePageInterface */
    private $createPage;

    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        CreatePageInterface $createPage
    )
    {
        $this->createPage = $createPage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to create a new Iyzico payment method
     */
    public function iWantToCreateANewIyzicoPaymentMethod(): void
    {
        $this->createPage->open(['factory' => 'iyzico']);
    }

    /**
     * @When I configure it with test Iyzico credentials
     */
    public function iConfigureItWithTestIyzicoCredentials(): void
    {
        $this->resolveCurrentPage()->setEnvironment('sandbox');
        $this->resolveCurrentPage()->setApiKey('test');
        $this->resolveCurrentPage()->setSecretKey('test');
        $this->resolveCurrentPage()->setThreeds(false);
    }

    /**
     * @return CreatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);
    }
}
