<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Form\Type;

use App\Entity\Customer\Customer;
use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Eres\SyliusIyzicoPlugin\Entity\Customer\CustomerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;


final class IyzicoGatewayConfigurationType extends AbstractType
{
    /**
     * @var FlashBag
     */
    private $flashBag;

    public function __construct(FlashBag $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('api_key', TextType::class, [
                'label' => 'eres_sylius_iyzico_plugin.ui.api_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'eres_sylius_iyzico_plugin.ui.api_key.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'eres_sylius_iyzico_plugin.ui.secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'eres_sylius_iyzico_plugin.ui.secret_key.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'eres_sylius_iyzico_plugin.ui.sandbox' => IyzicoBridgeInterface::SANDBOX_ENVIRONMENT,
                    'eres_sylius_iyzico_plugin.ui.production' => IyzicoBridgeInterface::PRODUCTION_ENVIRONMENT,
                ],
                'label' => 'eres_sylius_iyzico_plugin.ui.environment',
                'constraints' => [
                    new NotBlank([
                        'message' => 'eres_sylius_iyzico_plugin.environment.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if (!method_exists(Customer::class, 'getIdentityNumber')) {
                }
                $this->flashBag->set('error', 'Iyzico payment plugin Installation failed');
            });
    }
}
