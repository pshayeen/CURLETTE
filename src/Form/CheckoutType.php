<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fulfillmentMethod', ChoiceType::class, [
                'label' => 'Order Type',
                'choices' => ['Delivery' => 'delivery', 'Pickup' => 'pickup'],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [new NotBlank(message: 'Please choose delivery or pickup.')],
            ])
            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'Delivery Address',
                'required' => false,
                'attr' => ['rows' => 3, 'maxlength' => 255, 'placeholder' => 'House no., street, barangay, city'],
                'constraints' => [
                    new Length(max: 255, maxMessage: 'The address is too long.'),
                ],
            ])
            ->add('saveAddress', CheckboxType::class, [
                'label' => 'Save this address to my profile',
                'required' => false,
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Payment Method',
                'choices' => ['Cash on Delivery / Cash on Pickup' => 'cash', 'GCash' => 'gcash', 'Bank Transfer' => 'bank'],
                'expanded' => true,
                'multiple' => false,
                'data' => 'cash',
                'constraints' => [new NotBlank(message: 'Please choose a payment method.')],
            ])
            ->add('paymentReference', TextType::class, [
                'label' => 'Reference Number',
                'required' => false,
                'attr' => ['maxlength' => 100, 'placeholder' => 'e.g. 1234567890123'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
