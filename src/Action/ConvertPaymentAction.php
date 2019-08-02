<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Action;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\PaymentInterface;


class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $order = $payment->getOrder();

        $details = [
            'iyzico_id' => $order->getId(),
            'iyzico_currency_code' => $order->getCurrencyCode(),
            'iyzico_order_number' => $order->getNumber(),
            'iyzico_total' => $order->getTotal() / 100,
            'iyzico_local_code' => explode("_", $order->getLocaleCode())[0],
            'iyzico_target_url' => $request->getToken()->getTargetUrl(),
            'iyzico_shipping_address' => [
                "firstname" => $order->getShippingAddress()->getFirstName(),
                "lastname" => $order->getShippingAddress()->getLastName(),
                "country_code" => $order->getShippingAddress()->getCountryCode(),
                "city" => $order->getShippingAddress()->getCity(),
                "street" => $order->getShippingAddress()->getStreet(),
                "postcode" => $order->getShippingAddress()->getPostcode(),
            ],
            'iyzico_billing_address' => [
                'firstname' => $order->getBillingAddress()->getFirstName(),
                'lastname' => $order->getBillingAddress()->getLastName(),
                'country_code' => $order->getBillingAddress()->getCountryCode(),
                'city' => $order->getBillingAddress()->getCity(),
                'street' => $order->getBillingAddress()->getStreet(),
                'postcode' => $order->getBillingAddress()->getPostcode(),
            ],
            'iyzico_customer' => [
                'id' => $order->getCustomer()->getId(),
                'firstname' => $order->getCustomer()->getFirstName() ? $order->getCustomer()->getFirstName() : $order->getShippingAddress()->getFirstName(),
                'lastname' => $order->getCustomer()->getLastName() ? $order->getCustomer()->getLastName() : $order->getShippingAddress()->getLastName(),
                'phone_number' => $order->getCustomer()->getPhoneNumber() ? $order->getCustomer()->getPhoneNumber() : $order->getShippingAddress()->getPhoneNumber(),
                'email' => $order->getCustomer()->getEmail(),
                'ip' => $order->getCustomerIp(),
                'last_login_date' => $order->getCustomer()->getUser() ? $order->getCustomer()->getUser()->getLastLogin() : null,
                'registration_date' => $order->getCustomer()->getCreatedAt(),
                'identity_number' => $_ENV['APP_ENV'] === 'test' ? '74300864791' : $order->getCustomer()->getIdentityNumber(),
                'country_code' => $order->getShippingAddress()->getCountryCode(),
                'city' => $order->getShippingAddress()->getCity(),
                'street' => $order->getShippingAddress()->getStreet(),
                'postcode' => $order->getShippingAddress()->getPostcode(),
            ],
            'iyzico_items' => array_map(function ($orderItem) {
                return [
                    'id' => $orderItem->getId(),
                    'name' => $orderItem->getProductName(),
                    'total' => $orderItem->getTotal() / 100,
                    'category' => $orderItem->getProduct()->getMainTaxon() ? $orderItem->getProduct()->getMainTaxon()->getName() : "empty"
                ];
            }, iterator_to_array($order->getItems())),
            'iyzico_shipment' => array_map(function ($shipment) use ($order) {
                return [
                    'id' => $shipment->getMethod()->getId(),
                    'name' => $shipment->getMethod()->getName(),
                    'total' => $order->getShippingTotal() / 100,
                    'category' => $shipment->getMethod()->getCategory() ? $shipment->getMethod()->getCategory()->getName() : IyzicoBridgeInterface::SHIPPING_CATEGORY
                ];
            }, iterator_to_array($order->getShipments()))
        ];

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array';
    }
}
