<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Tests\Eres\SyliusIyzicoPlugin\Behat\Page\External\IyzicoCheckoutPageInterface;
use Tests\Eres\SyliusIyzicoPlugin\Behat\Service\Mocker\IyzicoApiMocker;


class IyzicoCheckoutContext implements Context
{
    /** @var CompletePageInterface */
    private $summaryPage;

    /** @var IyzicoCheckoutPageInterface */
    private $iyzicoCheckoutPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    /** @var EntityRepository */
    private $paymentRepository;

    /** @var IyzicoApiMocker */
    private $iyzicoApiMocker;

    public function __construct(
        CompletePageInterface $summaryPage,
        IyzicoApiMocker $iyzicoApiMocker,
        IyzicoCheckoutPageInterface $iyzicoCheckoutPage,
        ShowPageInterface $orderDetails
    )
    {
        $this->summaryPage = $summaryPage;
        $this->iyzicoApiMocker = $iyzicoApiMocker;
        $this->iyzicoCheckoutPage = $iyzicoCheckoutPage;
        $this->orderDetails = $orderDetails;
    }

    /**
     * @When I confirm my order with Iyzico payment
     * @Given I have confirmed my order with Iyzico payment
     */
    public function iConfirmMyOrderWithIyzicoPayment(): void
    {
        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () {
            $this->summaryPage->confirmOrder();
        });

    }

    /**
     * @When I sign in to Iyzico and pay successfully
     *
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function iSignInToIyzicoAndPaySuccessfully(): void
    {
        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () {
            $this->iyzicoCheckoutPage->pay();
        });
    }

    /**
     * @When I try to pay again Iyzico payment
     */
    public function iTryToPayAgainIyzicoPayment(): void
    {
        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () {
            $this->iyzicoCheckoutPage->pay();
        });
    }

    /**
     * @When I sign in to Iyzico and pay fail :number :securityCode :year
     * @When I sign in to Iyzico and pay fail :number
     */
    public function iFailedMyIyzicoPayment($number, $securityCode = 123, $year = 2025): void
    {
        $this->iyzicoApiMocker->mockApiSuccessfulVerifyTransaction(function () use ($number, $securityCode, $year) {
            $this->iyzicoCheckoutPage->failedPayment($number, $securityCode, $year);
        });
    }


    /**
     * @Then I should be notified that my payment has been failed :expectedNotification
     */
    public function assertNotification($expectedNotification): void
    {
        $notifications = $this->orderDetails->getNotifications();
        $hasNotifications = '';

        foreach ($notifications as $notification) {
            $hasNotifications .= $notification;
            if ($notification === $expectedNotification) {
                return;
            }
        }

        throw new \RuntimeException(sprintf('There is no notificaiton with "%s". Got "%s"', $expectedNotification, $hasNotifications));
    }
}
