<?php

namespace App\Form;

use App\Entity\Appointments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName', TextType::class, ['label' => 'Customer name'])
            ->add('customerEmail', EmailType::class, ['label' => 'Customer email'])
            ->add('service', TextType::class, [
                'attr' => ['placeholder' => 'e.g. Curl Styling'],
            ])
            ->add('appointmentDate', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('appointmentTime', TimeType::class, [
                'label' => 'Time',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Upcoming' => 'upcoming',
                    'Completed' => 'completed',
                    'Cancelled' => 'cancelled',
                ],
            ])
            ->add('depositAmount', NumberType::class, [
                'label' => 'Deposit (PHP)',
                'scale' => 2,
                'html5' => true,
                'input' => 'string',
                'attr' => ['min' => 0, 'step' => '0.01'],
                'constraints' => [new Assert\PositiveOrZero(message: 'The deposit cannot be negative.')],
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
        $resolver->setDefaults(['data_class' => Appointments::class]);
    }
}