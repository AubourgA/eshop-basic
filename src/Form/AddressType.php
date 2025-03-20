<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street')
            ->add('city')
            ->add('postalCode')
            ->add('country')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Adresse de facturation' => 'facturation',
                    'Adresse de livraison' => 'livraison',
                ],
                'expanded' => true,  // Affiche sous forme de boutons radio
                'multiple' => false, // Un seul choix possible
            ])
            ->add('isPrimary')
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
