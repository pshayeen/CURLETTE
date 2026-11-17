<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full Name',
                'empty_data' => '',
                'attr' => ['maxlength' => 100],
                'constraints' => [
                    new NotBlank(message: 'Please enter your full name.'),
                    new Length(max: 100),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
                'attr' => ['maxlength' => 180],
                'constraints' => [
                    new NotBlank(message: 'Please enter your email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone Number',
                'empty_data' => '',
                'attr' => ['maxlength' => 30],
                'constraints' => [
                    new NotBlank(message: 'Please enter your phone number.'),
                    new Regex(pattern: '/^[0-9+\-\s()]{7,20}$/', message: 'Please enter a valid phone number.'),
                ],
            ])
            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'Saved Delivery Address <span>(OPTIONAL)</span>',
                'label_html' => true,
                'required' => false,
                'attr' => ['rows' => 3, 'maxlength' => 255, 'placeholder' => 'House no., street, barangay, city'],
                'constraints' => [
                    new Length(max: 255, maxMessage: 'The address is too long.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
