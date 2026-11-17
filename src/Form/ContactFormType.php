<?php

namespace App\Form;

use App\Entity\Messages;
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

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('senderName', TextType::class, [
                'label' => 'Name',
                'empty_data' => '',
                'attr' => ['maxlength' => 100],
                'constraints' => [
                    new NotBlank(message: 'Please enter your name.'),
                    new Length(max: 100),
                ],
            ])
            ->add('senderEmail', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
                'attr' => ['maxlength' => 180],
                'constraints' => [
                    new NotBlank(message: 'Please enter your email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('senderPhone', TelType::class, [
                'label' => 'Phone (optional)',
                'required' => false,
                'attr' => ['maxlength' => 30],
                'constraints' => [
                    new Regex(
                        pattern: '/^[0-9+\-\s()]{7,20}$/',
                        message: 'Please enter a valid phone number.',
                    ),
                ],
            ])
            ->add('messageText', TextareaType::class, [
                'label' => 'Message',
                'empty_data' => '',
                'attr' => ['rows' => 5, 'placeholder' => 'Tell us what’s on your mind...'],
                'constraints' => [
                    new NotBlank(message: 'Please write a message.'),
                    new Length(min: 5, max: 2000, minMessage: 'Your message is a little short.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Messages::class]);
    }
}
