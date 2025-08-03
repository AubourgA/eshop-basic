<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Enum\MarketingPosition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Choisissez une catégorie',
                'required' => false, 
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
            ])
          
             ->add('purchasePrice', NumberType::class, [
                'label' => 'Coût d’achat (€)',
                'required' => true,
            ])
            ->add('marketingPosition', EnumType::class, [
                    'class' => MarketingPosition::class,
                    'label' => 'Positionnement marketing',
                    'choices' => MarketingPosition::choices(),
                    'placeholder' => 'Aucun',
                    'required' => false,
                     'choice_label' => fn (MarketingPosition $choice) => $choice->label(), // optionnel si tu veux afficher un label plus propre
                ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
