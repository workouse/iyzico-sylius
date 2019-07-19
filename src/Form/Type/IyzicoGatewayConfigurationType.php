<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Form\Type;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class IyzicoGatewayConfigurationType extends AbstractType
{
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
            ]);
    }
}
