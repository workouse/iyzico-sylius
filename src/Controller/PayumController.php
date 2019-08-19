<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Controller;

use Eres\SyliusIyzicoPlugin\Event\PaymentEvent;
use FOS\RestBundle\View\View;
use Payum\Core\Payum;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PayumController
{
    /** @var Payum */
    private $payum;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MetadataInterface */
    private $orderMetadata;

    /** @var RequestConfigurationFactoryInterface */
    private $requestConfigurationFactory;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var RouterInterface */
    private $router;

    /** @var GetStatusFactoryInterface */
    private $getStatusRequestFactory;

    /** @var ResolveNextRouteFactoryInterface */
    private $resolveNextRouteRequestFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Payum $payum,
        OrderRepositoryInterface $orderRepository,
        MetadataInterface $orderMetadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RouterInterface $router,
        GetStatusFactoryInterface $getStatusFactory,
        ResolveNextRouteFactoryInterface $resolveNextRouteFactory,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->payum = $payum;
        $this->orderRepository = $orderRepository;
        $this->orderMetadata = $orderMetadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->viewHandler = $viewHandler;
        $this->router = $router;
        $this->getStatusRequestFactory = $getStatusFactory;
        $this->resolveNextRouteRequestFactory = $resolveNextRouteFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function afterCaptureAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->orderMetadata, $request);

        $token = $this->getHttpRequestVerifier()->verify($request);

        /** @var Generic|GetStatusInterface $status */
        $status = $this->getStatusRequestFactory->createNewWithModel($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);

        $resolveNextRoute = $this->resolveNextRouteRequestFactory->createNewWithModel($status->getFirstModel());
        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        $this->getHttpRequestVerifier()->invalidate($token);

        if (PaymentInterface::STATE_NEW !== $status->getValue()) {

            $model = $status->getModel();

            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');

            if (isset($model['iyzico_error_message']) && $model['iyzico_error_message']) {
                $flashBagMessage = $model['iyzico_error_message'];
            } else {
                $flashBagMessage = sprintf('sylius.payment.%s', $status->getValue());
            }
            $flashBag->add('info', $flashBagMessage);

        }

        $paymentEvent = new PaymentEvent();
        $paymentEvent->setPayment($status->getFirstModel());
        $this->eventDispatcher->dispatch('eres_sylius_iyzico_plugin.payment.post', $paymentEvent);

        return $this->viewHandler->handle(
            $configuration,
            View::createRouteRedirect($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters())
        );
    }

    private function getHttpRequestVerifier(): HttpRequestVerifierInterface
    {
        return $this->payum->getHttpRequestVerifier();
    }
}
