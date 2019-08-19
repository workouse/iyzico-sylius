<?php


namespace Eres\SyliusIyzicoPlugin\Event;

use App\Entity\Payment\Payment;
use Symfony\Component\EventDispatcher\Event;

class PaymentEvent extends Event
{
    /**
     * @var Payment
     */
    private $payment;

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }
}
