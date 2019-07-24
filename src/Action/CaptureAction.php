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

    private $threeds;

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
        $this->threeds = $api['threeds'];
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

        $mdStatus = $this->requestStack->getMasterRequest()->request->get('mdStatus');
        if ($this->threeds && isset($mdStatus)) {
            $status = $this->requestStack->getMasterRequest()->request->get('status');
            if ($status === IyzicoBridgeInterface::COMPLETED_STATUS) {
                $data = $this->requestStack->getMasterRequest()->request->all();
                $data['iyzico_local_code'] = $model['iyzico_local_code'];
                $payment = $this->iyzicoBridge->createThreeds($data);
                $model['iyzico_status'] = $payment['status'];
                $model['iyzico_error_message'] = $payment['error_message'];
            } else {
                $model['iyzico_status'] = IyzicoBridgeInterface::FAILED_STATUS;
                $model['iyzico_error_message'] = $this->getMdStatus($mdStatus);
            }

            return;
        }

        $form = $this->createCreditCardForm();
        $form->handleRequest($this->requestStack->getMasterRequest());
        if ($form->isSubmitted()) {
            /** @var CreditCardInterface $card */
            $card = $form->getData();
            $card->secure();

            if ($form->isValid()) {
                $model['iyzico_card'] = $card;
                $model['iyzico_threeds'] = $this->threeds;
                $payment = $this->iyzicoBridge->create($model);
                $model['iyzico_status'] = $payment['status'];
                $model['iyzico_error_message'] = $payment['error_message'];

                if ($model['iyzico_threeds'] && $model['iyzico_status'] === 'success') {
                    $model['iyzico_html_content'] = $payment['html_content'];
                    throw new HttpResponse($payment['html_content'], 200, [
                        'Cache-Control' => 'no-store, no-cache, max-age=0, post-check=0, pre-check=0',
                        'X-Status-Code' => 200,
                        'Pragma' => 'no-cache',
                    ]);
                }
                return;
            }
        }

        $this->gateway->execute(
            $renderTemplate =
                new RenderTemplate("@EresSyliusIyzicoPlugin\credit_card_form.html.twig", [
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

    private function getMdStatus($mdStatus): string
    {
        $result = "eres_sylius_iyzico_plugin.payment.invalid_3d_secure_signature_or_verification";

        if ($mdStatus == 2) {
            $result = "eres_sylius_iyzico_plugin.payment.card_holder_or_issuer_not_registered_to_3d_secure_network";
        } elseif ($mdStatus == 3) {
            $result = "eres_sylius_iyzico_plugin.payment.issuer_is_not_registered_to_3d_secure_network";
        } elseif ($mdStatus == 4) {
            $result = "eres_sylius_iyzico_plugin.payment.verification_is_not_possible_card_holder_chosen_to_register_later_on_system";
        } elseif ($mdStatus == 5) {
            $result = "eres_sylius_iyzico_plugin.payment.verification_is_not_possbile";
        } elseif ($mdStatus == 6) {
            $result = "eres_sylius_iyzico_plugin.payment.3d_secure_error";
        } elseif ($mdStatus == 7) {
            $result = "eres_sylius_iyzico_plugin.payment.system_error";
        } elseif ($mdStatus == 8) {
            $result = "eres_sylius_iyzico_plugin.payment.unknown_card";
        }

        return $result;
    }

}
