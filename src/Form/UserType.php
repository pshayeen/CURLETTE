<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordRequired = $options['require_password'];

        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full Name',
                'attr' => ['maxlength' => 100],
                'constraints' => [
                    new NotBlank(message: 'Please enter the full name.'),
                    new Length(max: 100),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'empty_data' => '',
                'attr' => ['maxlength' => 30, 'autocomplete' => 'off'],
                'constraints' => [
                    new NotBlank(message: 'Please choose a username.'),
                    new Length(min: 3, max: 30, minMessage: 'The username should be at least {{ limit }} characters.'),
                    new Regex(pattern: '/^[A-Za-z0-9_.]+$/', message: 'Use letters, numbers, dots and underscores only.'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['maxlength' => 180],
                'constraints' => [
                    new NotBlank(message: 'Please enter an email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone (optional)',
                'required' => false,
                'attr' => ['maxlength' => 30],
                'constraints' => [
                    new Regex(pattern: '/^[0-9+\-\s()]{7,20}$/', message: 'Please enter a valid phone number.'),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $passwordRequired ? 'Password' : 'New Password (leave empty to keep)',
                'mapped' => false,
                'required' => $passwordRequired,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => $passwordRequired
                    ? [
                        new NotBlank(message: 'Please enter a password.'),
                        new Length(
                            min: 8,
                            minMessage: 'The password should be at least {{ limit }} characters.',
                            max: 4096,
                        ),
                    ]
                    : [
                        new Length(
                            min: 8,
                            minMessage: 'The password should be at least {{ limit }} characters.',
                            max: 4096,
                        ),
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => true,
        ]);
    }
}