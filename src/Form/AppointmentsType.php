<?php

namespace App\Form;

use App\Entity\Appointments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName')
            ->add('customerEmail')
            ->add('service')
            ->add('appointmentDate', null, [
                'widget' => 'single_text'
            ])
            ->add('appointmentTime', null, [
                'widget' => 'single_text'
            ])
            ->add('notes')
            ->add('status')
            ->add('depositAmount')
            ->add('paymentReference')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointments::class,
        ]);
    }
}
