<?php

namespace App\Form;

use App\Entity\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceName', TextType::class, [
                'label' => 'Service Name',
                'attr' => ['maxlength' => 150],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price (PHP)',
                'scale' => 2,
                'html5' => true,
                'input' => 'string',
                'attr' => ['min' => 0.01, 'step' => '0.01'],
                'constraints' => [new Assert\Positive(message: 'The price must be more than 0.')],
            ])
            ->add('duration', TextType::class, [
                'label' => 'Duration',
                'attr' => ['maxlength' => 50, 'placeholder' => 'e.g. 60-90 min'],
            ])
            ->add('serviceDescription', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'What is this service?'],
            ])
            ->add('serviceIncludes', TextareaType::class, [
                'label' => 'What’s Included',
                'attr' => ['rows' => 3, 'placeholder' => 'e.g. Wash, styling products, curl definition, and finishing.'],
                'constraints' => [new Assert\NotBlank(message: 'Please say what is included.')],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'help' => 'JPG, PNG or WebP, up to 3 MB. Leave empty to keep the current photo.',
                'attr' => ['accept' => 'image/*'],
                'constraints' => [
                    new Assert\File(
                        maxSize: '3M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Please upload a JPG, PNG or WebP image.',
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Services::class]);
    }
}