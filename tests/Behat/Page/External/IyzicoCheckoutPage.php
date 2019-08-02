<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Page\External;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tests\Eres\SyliusIyzicoPlugin\Behat\Service\Mocker\IyzicoApiMocker;
use Symfony\Component\BrowserKit\Client;

final class IyzicoCheckoutPage extends Page implements IyzicoCheckoutPageInterface
{
    /** @var IyzicoApiMocker */
    private $iyzicoApiMocker;

    /** @var RepositoryInterface */
    private $securityTokenRepository;

    /** @var EntityRepository */
    private $paymentRepository;

    /** @var Client */
    private $client;

    public function __construct(
        Session $session,
        MinkParameters $parameters,
        IyzicoApiMocker $iyzicoApiMocker,
        RepositoryInterface $securityTokenRepository,
        Client $client
    )
    {
        parent::__construct($session, $parameters);
        $this->iyzicoApiMocker = $iyzicoApiMocker;
        $this->securityTokenRepository = $securityTokenRepository;
        $this->client = $client;
    }

    public function pay(): void
    {
        $captureToken = $this->findToken('after');

        $postData = [
            'credit_card[holder]' => 'Ömer Büyükçelik',
            'credit_card[number]' => '4054180000000007',
            'credit_card[securityCode]' => '123',
            'credit_card[expireAt][month]' => "12",
            'credit_card[expireAt][day]' => "1",
            'credit_card[expireAt][year]' => "2025",
        ];

        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () use ($captureToken, $postData) {
            $this->client->request('POST', $captureToken->getTargetUrl(), $postData);
            $crawler = $this->client->submitForm('Pay', $postData);
            sleep(1);
            $this->getDriver()->visit($captureToken->getAfterUrl());
        });
    }

    public function failedPayment($number, $securityCode, $year): void
    {
        $captureToken = $this->findToken('after');

        $postData = [
            'credit_card[holder]' => 'Ömer Büyükçelik',
            'credit_card[number]' => $number,
            'credit_card[securityCode]' => $securityCode,
            'credit_card[expireAt][month]' => "12",
            'credit_card[expireAt][day]' => "1",
            'credit_card[expireAt][year]' => $year,
        ];

        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () use ($captureToken, $postData) {
            $this->client->request('POST', $captureToken->getTargetUrl(), $postData);
            $this->client->submitForm('Pay', $postData);
            sleep(1);
            $this->getDriver()->visit($captureToken->getAfterUrl());
        });
    }

    protected function getUrl(array $urlParameters = []): string
    {
        dd("getUrl");
    }

    private function findToken(string $type = 'capture'): TokenInterface
    {
        $tokens = [];

        /** @var TokenInterface $token */
        foreach ($this->securityTokenRepository->findAll() as $token) {
            $tokens[] = $token;

        }

        if (count($tokens) > 0) {
            return end($tokens);
        }

        throw new \RuntimeException('Cannot find capture token, check if you are after proper checkout steps');
    }
}
