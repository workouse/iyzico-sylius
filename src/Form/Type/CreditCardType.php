<?php


namespace Eres\SyliusIyzicoPlugin\Form\Type;

use Payum\Core\Bridge\Symfony\Form\Type\CreditCardExpirationDateType;
use Payum\Core\Model\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditCardType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('holder', TextType::class, [
                'label' => 'form.credit_card.holder',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('number', TextType::class, [
                'label' => 'form.credit_card.number',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('securityCode', TextType::class, [
                'label' => 'form.credit_card.security_code',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add(
                'expireAt',
                CreditCardExpirationDateType::class,
                [
                    'input' => 'datetime',
                    'widget' => 'choice',
                    'label' => 'form.credit_card.expire_at',
                    'format' => 'dd MM yyyy',
                    'attr' => [
                        'autocomplete' => 'off'
                    ]
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'data_class' => CreditCard::class,
                    'validation_groups' => array('Payum'),
                    'label' => false,
                    'translation_domain' => 'PayumBundle',
                )
            );
    }
}
