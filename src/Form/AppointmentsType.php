<?php

namespace App\Form;

use App\Entity\Appointments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $appointment = $options['data'] ?? null;

        $serviceChoices = [];
        foreach ($options['services'] as $service) {
            $serviceChoices[$service->getServiceName()] = $service->getServiceName();
        }
        if ($appointment && $appointment->getService() && !isset($serviceChoices[$appointment->getService()])) {
            $serviceChoices[$appointment->getService()] = $appointment->getService();
        }

        $timeChoices = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            foreach ([0, 30] as $minute) {
                if (17 === $hour && 30 === $minute) {
                    continue;
                }
                $value = sprintf('%02d:%02d', $hour, $minute);
                $timeChoices[(new \DateTimeImmutable($value))->format('g:i A')] = $value;
            }
        }

        $paymentChoices = ['GCash' => 'gcash', 'Bank Transfer' => 'bank'];
        if ($appointment && 'cash' === $appointment->getPaymentMethod()) {
            $paymentChoices['Cash'] = 'cash';
        }

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
            ->add('service', ChoiceType::class, [
                'label' => 'SERVICE',
                'required' => false,
                'choices' => $serviceChoices,
                'placeholder' => 'Choose a service',
                'empty_data' => '',
                'constraints' => [new Assert\NotBlank(message: 'Please choose a service.')],
            ])
            ->add('appointmentDate', DateType::class, [
                'label' => 'APPOINTMENT DATE',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'invalid_message' => 'Please choose a date.',
                'attr' => ['min' => (new \DateTimeImmutable('today'))->format('Y-m-d')],
            ])
            ->add('appointmentTime', ChoiceType::class, [
                'label' => 'APPOINTMENT TIME',
                'choices' => $timeChoices,
                'placeholder' => 'Choose a time',
                'invalid_message' => 'Please choose a time.',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'NOTES <span>(OPTIONAL)</span>',
                'label_html' => true,
                'required' => false,
                'attr' => ['rows' => 4, 'placeholder' => 'Tell us anything you’d like us to know...'],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Payment Method',
                'choices' => $paymentChoices,
                'expanded' => true,
                'multiple' => false,
                'empty_data' => '',
                'constraints' => [new Assert\NotBlank(message: 'Please choose a payment method.')],
            ])
            ->add('paymentReference', TextType::class, [
                'label' => 'Reference Number',
                'attr' => ['maxlength' => 100, 'placeholder' => 'e.g. 1234567890123'],
                'constraints' => [new Assert\NotBlank(message: 'Please enter the reference number.')],
            ])
        ;

        $builder->get('appointmentTime')->addModelTransformer(new CallbackTransformer(
            fn (?\DateTimeInterface $time): ?string => $time?->format('H:i'),
            fn (?string $time): ?\DateTimeImmutable => $time ? \DateTimeImmutable::createFromFormat('H:i', $time) : null,
        ));

        if ($options['include_status']) {
            $builder->add('status', ChoiceType::class, [
                'choices' => [
                    'Upcoming' => 'upcoming',
                    'Completed' => 'completed',
                    'Cancelled' => 'cancelled',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointments::class,
            'services' => [],
            'include_status' => true,
        ]);
    }
}