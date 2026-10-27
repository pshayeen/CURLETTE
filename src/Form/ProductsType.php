<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName', TextType::class, [
                'label' => 'Product Name',
                'attr' => ['maxlength' => 255],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price (PHP)',
                'scale' => 2,
                'html5' => true,
                'input' => 'string',
                'attr' => ['min' => 0.01, 'step' => '0.01'],
                'constraints' => [new Assert\Positive(message: 'The price must be more than 0.')],
            ])
            ->add('stockQuantity', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['min' => 0],
                'constraints' => [new Assert\PositiveOrZero(message: 'Stock cannot be negative.')],
            ])
            ->add('productDescription', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'What is this product, what does it do...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Products::class]);
    }
}