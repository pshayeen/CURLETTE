<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName', TextType::class, ['label' => 'Customer name'])
            ->add('customerEmail', EmailType::class, ['label' => 'Customer email'])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Placed' => 'placed',
                    'Processing' => 'processing',
                    'Fulfilled' => 'fulfilled',
                    'Cancelled' => 'cancelled',
                ],
            ])
            ->add('total', NumberType::class, [
                'label' => 'Total (PHP)',
                'scale' => 2,
                'html5' => true,
                'input' => 'string',
                'attr' => ['min' => 0, 'step' => '0.01'],
                'constraints' => [new Assert\PositiveOrZero(message: 'The total cannot be negative.')],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Payment method',
                'choices' => [
                    'Cash on Delivery' => 'cash',
                    'GCash' => 'gcash',
                    'Bank Transfer' => 'bank',
                ],
            ])
            ->add('paymentReference', TextType::class, [
                'label' => 'Payment reference #',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Orders::class]);
    }
}