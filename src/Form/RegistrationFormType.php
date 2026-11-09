<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full name',
                'attr' => ['autocomplete' => 'name', 'maxlength' => 100],
                'constraints' => [
                    new NotBlank(message: 'Please enter your full name.'),
                    new Length(max: 100),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => ['autocomplete' => 'username', 'maxlength' => 30],
                'constraints' => [
                    new NotBlank(message: 'Please choose a username.'),
                    new Length(
                        min: 3,
                        max: 30,
                        minMessage: 'Your username should be at least {{ limit }} characters.',
                        maxMessage: 'Your username can be at most {{ limit }} characters.',
                    ),
                    new Regex(
                        pattern: '/^[A-Za-z0-9_.]+$/',
                        message: 'Use letters, numbers, dots and underscores only.',
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['autocomplete' => 'email', 'maxlength' => 180],
                'constraints' => [
                    new NotBlank(message: 'Please enter your email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone number (optional)',
                'required' => false,
                'attr' => ['autocomplete' => 'tel', 'maxlength' => 30, 'placeholder' => '0912 345 6789'],
                'constraints' => [
                    new Regex(
                        pattern: '/^[0-9+\-\s()]{7,20}$/',
                        message: 'Please enter a valid phone number.',
                    ),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
              
                'mapped' => false,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Repeat password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'The two passwords do not match.',
                'constraints' => [
                    new NotBlank(message: 'Please enter a password.'),
                    new Length(
                        min: 8,
                        minMessage: 'Your password should be at least {{ limit }} characters.',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the terms and privacy policy',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'You should agree to our terms.'),
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