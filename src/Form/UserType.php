<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\PasswordRules;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'empty_data' => '',
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
                'empty_data' => '',
                'attr' => ['maxlength' => 180],
                'constraints' => [
                    new NotBlank(message: 'Please enter an email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone',
                'empty_data' => '',
                'attr' => ['maxlength' => 30],
                'constraints' => [
                    new NotBlank(message: 'Please enter a phone number.'),
                    new Regex(pattern: '/^[0-9+\-\s()]{7,20}$/', message: 'Please enter a valid phone number.'),
                ],
            ])
                        ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'choices' => [
                    'Staff' => 'ROLE_STAFF',
                    'Admin' => 'ROLE_ADMIN',
                    'Customer' => 'ROLE_USER',
                ],
                'disabled' => $options['lock_role'],
            ])

            ->add('plainPassword', PasswordType::class, [
                'label' => $passwordRequired ? 'Password' : 'New Password (leave empty to keep)',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => $passwordRequired,
                'attr' => ['autocomplete' => 'new-password'],
                'help' => PasswordRules::HELP,
                'constraints' => $passwordRequired
                    ? PasswordRules::constraints()
                    : PasswordRules::constraints(false),
            ])
        ;

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function (?array $roles): string {
                $roles = $roles ?? [];

                if (in_array('ROLE_ADMIN', $roles, true)) {
                    return 'ROLE_ADMIN';
                }

                return in_array('ROLE_STAFF', $roles, true) ? 'ROLE_STAFF' : 'ROLE_USER';
            },
            fn (?string $role): array => [$role ?: 'ROLE_USER'],
        ));

        if ($options['include_status']) {
            $builder->add('isActive', CheckboxType::class, [
                'label' => 'Account is active',
                'required' => false,
                'disabled' => $options['lock_role'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => true,
            'include_status' => true,
            'lock_role' => false,
        ]);
    }
}