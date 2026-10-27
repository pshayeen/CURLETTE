<?php

namespace App\Form;

use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('senderName', TextType::class, ['label' => 'Name'])
            ->add('senderEmail', EmailType::class, ['label' => 'Email'])
            ->add('senderPhone', TextType::class, [
                'label' => 'Phone',
                'required' => false,
            ])
            ->add('messageText', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 5],
            ])
            ->add('isRead', CheckboxType::class, [
                'label' => 'Mark as read',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Messages::class]);
    }
}