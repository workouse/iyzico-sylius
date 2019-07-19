<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Bridge;

use GuzzleHttp\ClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IyzicoBridge implements IyzicoBridgeInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $environment = self::SANDBOX_ENVIRONMENT;

    /**
     * @var ClientInterface
     */
    private $client;


    public function setAuthorizationData(
        string $apiKey,
        string $secretKey,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->environment = $environment;

        $this->client = new \Iyzipay\Options();
        $this->client->setApiKey($this->apiKey);
        $this->client->setSecretKey($this->secretKey);
        $this->client->setBaseUrl($this->getHostForEnvironment());
    }

    public function create($data)
    {
        $card = $data['iyzico_card'];
        $customer = $data['iyzico_customer'];
        $shippingAddress = $data['iyzico_shipping_address'];
        $billingAddress = $data['iyzico_billing_address'];
        $basketItems = $data['iyzico_items'];
        $total = $data['iyzico_total'];
        $shipment = $data['iyzico_shipment'];
        $basketItems = array_merge($basketItems, $shipment);
        $orderNumber = $data['iyzico_order_number'];
        $orderId = $data['iyzico_id'];
        $localCode = explode("_", $data['iyzico_local_code'])[0];
        $currencyCode = $data['iyzico_currency_code'];

        $request = new \Iyzipay\Request\CreatePaymentRequest();
        $request->setLocale($localCode);
        $request->setConversationId($orderId);
        $request->setPrice($total);
        $request->setPaidPrice($total);
        $request->setCurrency($currencyCode);
        $request->setInstallment(1);
        $request->setBasketId($orderNumber);
        $request->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setPaymentCard($this->setPaymentCard($card));
        $request->setBuyer($this->setBuyer($customer));
        $request->setShippingAddress($this->setShippingAddress($shippingAddress));
        $request->setBillingAddress($this->setBillingAddress($billingAddress));
        $request->setBasketItems($this->setBasketItems($basketItems));

        $payment = \Iyzipay\Model\Payment::create($request, $this->client);

        return $payment->getStatus();

    }

    private function setPaymentCard($card)
    {
        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setCardHolderName($card->getHolder());
        $paymentCard->setCardNumber($card->getNumber());
        $paymentCard->setExpireMonth($card->getExpireAt()->format('w'));
        $paymentCard->setExpireYear($card->getExpireAt()->format('y'));
        $paymentCard->setCvc($card->getSecurityCode());
        $paymentCard->setRegisterCard(0);
        return $paymentCard;
    }

    private function setBuyer($customer)
    {
        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId($customer['id']);
        $buyer->setName($customer['firstname']);
        $buyer->setSurname($customer['lastname']);
        $buyer->setGsmNumber($customer['phone_number']);
        $buyer->setEmail($customer['email']);
        $buyer->setIdentityNumber($customer['identity_number']);
        $buyer->setLastLoginDate($customer['last_login_date'] ? $customer['last_login_date']->format('Y-m-d H:i:s') : null);
        $buyer->setRegistrationDate($customer['registration_date']->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($customer['street']);
        $buyer->setIp($customer['ip']);
        $buyer->setCity($customer['city']);
        $buyer->setCountry($customer['country_code']);
        $buyer->setZipCode($customer['postcode']);

        return $buyer;
    }

    private function setShippingAddress($address)
    {
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($address['firstname'] . " " . $address['lastname']);
        $shippingAddress->setCity($address['city']);
        $shippingAddress->setCountry($address['country_code']);
        $shippingAddress->setAddress($address['street']);
        $shippingAddress->setZipCode($address['postcode']);
        return $shippingAddress;
    }

    private function setBillingAddress($address)
    {
        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($address['firstname'] . " " . $address['lastname']);
        $billingAddress->setCity($address['city']);
        $billingAddress->setCountry($address['country_code']);
        $billingAddress->setAddress($address['street']);
        $billingAddress->setZipCode($address['postcode']);
        return $billingAddress;
    }

    private function setBasketItems($items)
    {
        $basketItems = [];
        foreach ($items as $item) {
            if ($item['total'] > 0) {
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId($item['id']);
                $basketItem->setName($item['name']);
                $basketItem->setCategory1($item['category']);
                $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $basketItem->setPrice($item['total']);
                $basketItems[] = $basketItem;
            }
        }
        return $basketItems;
    }

    public function getHostForEnvironment(): string
    {
        return
            self::SANDBOX_ENVIRONMENT === $this->environment
                ? self::SANDBOX_HOST : self::PRODUCTION_HOST;
    }

}
