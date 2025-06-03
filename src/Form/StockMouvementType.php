<?php

namespace App\Form;

use App\Entity\StockMouvement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockMouvementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de mouvement',
                'choices' => [
                    'Entrée' => 'IN',
                    'Sortie' => 'OUT',
                ],
            ])
              ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
            ])
              ->add('comments', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockMouvement::class,
        ]);
    }
}
