<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Action;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Payum\Core\Bridge\Symfony\Form\Type\CreditCardType;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\CreditCardInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\RenderTemplate;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;


final class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var IyzicoBridgeInterface
     */
    private $iyzicoBridge;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        IyzicoBridgeInterface $iyzicoBridge,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack
    )
    {
        $this->iyzicoBridge = $iyzicoBridge;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    public function setApi($api)
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported. Expected to be set as array.');
        }

        $this->iyzicoBridge->setAuthorizationData($api['api_key'], $api['secret_key'], $api['environment']);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $form = $this->createCreditCardForm();
        $form->handleRequest($this->requestStack->getMasterRequest());

        if ($form->isSubmitted()) {
            /** @var CreditCardInterface $card */
            $card = $form->getData();
            $card->secure();

            if ($form->isValid()) {
                $model['iyzico_card'] = $card;
                $model['iyzico_status'] = $this->iyzicoBridge->create($model);
                return;
            }
        }

        $this->gateway->execute(
            $renderTemplate =
                new RenderTemplate("EresSyliusIyzicoPlugin::credit_card_form.html.twig", [
                    'form' => $form->createView(),
                ]));

        throw new HttpResponse($renderTemplate->getResult(), 200, [
            'Cache-Control' => 'no-store, no-cache, max-age=0, post-check=0, pre-check=0',
            'X-Status-Code' => 200,
            'Pragma' => 'no-cache',
        ]);


    }

    /**
     * @return FormInterface
     */
    protected function createCreditCardForm()
    {
        return $this->formFactory->create(CreditCardType::class);
    }

}
