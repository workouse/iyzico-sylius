<?php


namespace Eres\SyliusIyzicoPlugin\Event;

use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\Event;

class PaymentEvent extends Event
{
    /**
     * @var PaymentInterface
     */
    private $payment;

    public function getPayment(): ?PaymentInterface
    {
        return $this->payment;
    }

    public function setPayment(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }
}
