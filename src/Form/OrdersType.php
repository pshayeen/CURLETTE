<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName', TextType::class, [
                'label' => 'CUSTOMER NAME',
                'empty_data' => '',
                'attr' => ['maxlength' => 100, 'placeholder' => 'Full name'],
                'constraints' => [new Assert\NotBlank(message: 'Please enter the customer name.')],
            ])
            ->add('customerEmail', EmailType::class, [
                'label' => 'CUSTOMER EMAIL',
                'empty_data' => '',
                'attr' => ['maxlength' => 180, 'placeholder' => 'name@email.com'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Please enter the customer email.'),
                    new Assert\Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('customerPhone', TelType::class, [
                'label' => 'CUSTOMER PHONE',
                'required' => $options['require_contact'],
                'attr' => ['maxlength' => 30, 'placeholder' => '0912 345 6789'],
                'constraints' => array_filter([
                    $options['require_contact'] ? new Assert\NotBlank(message: 'Please enter the customer phone number.') : null,
                    new Assert\Regex(pattern: '/^[0-9+\-\s()]{7,20}$/', message: 'Please enter a valid phone number.'),
                ]),
            ])
            ->add('fulfillmentMethod', ChoiceType::class, [
                'label' => 'Order type',
                'choices' => ['Walk-in pickup' => 'pickup', 'Delivery' => 'delivery'],
                'expanded' => true,
                'multiple' => false,
                'empty_data' => '',
                'constraints' => [new Assert\NotBlank(message: 'Please choose pickup or delivery.')],
            ])
            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'DELIVERY ADDRESS',
                'required' => false,
                'attr' => ['rows' => 3, 'maxlength' => 255, 'placeholder' => 'House no., street, barangay, city'],
                'constraints' => [
                    new Assert\Length(max: 255, maxMessage: 'The address is too long.'),
                ],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Payment Method',
                'choices' => ['GCash' => 'gcash', 'Bank Transfer' => 'bank', 'Cash on Delivery / Cash on Pickup' => 'cash'],
                'expanded' => true,
                'multiple' => false,
                'empty_data' => '',
                'constraints' => [new Assert\NotBlank(message: 'Please choose a payment method.')],
            ])
            ->add('paymentReference', TextType::class, [
                'label' => 'Reference Number <span>(NOT NEEDED FOR CASH)</span>',
                'label_html' => true,
                'required' => false,
                'attr' => ['maxlength' => 100, 'placeholder' => 'e.g. 1234567890123'],
            ])
        ;

        if ($options['include_status']) {
            $builder->add('status', ChoiceType::class, [
                'choices' => [
                    'Placed' => 'placed',
                    'Processing' => 'processing',
                    'Fulfilled' => 'fulfilled',
                    'Cancelled' => 'cancelled',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
            'include_status' => true,
            'require_contact' => false,
        ]);
    }
}