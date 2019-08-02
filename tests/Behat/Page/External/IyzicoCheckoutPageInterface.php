<?php

declare(strict_types=1);

namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Page\External;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface IyzicoCheckoutPageInterface extends PageInterface
{
    public function pay(): void;

    public function failedPayment($number, $securityCode, $year): void;

}
