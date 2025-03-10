<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('firstname', TextType::class, [
            'label'       => 'Prénom',
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                new Length([
                    'max' => 100,
                    'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('lastname', TextType::class, [
            'label'       => 'Nom',
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer votre nom']),
                new Length([
                    'max' => 100,
                    'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('phone', TextType::class, [
            'label'       => 'Téléphone',
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone']),
                new Length([
                    'max' => 10,
                    'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            'label' => 'Mot de passe',
            // Ce champ n'est pas mappé directement sur l'entité Customer (car on stockera le mot de passe haché)
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
            ],
        ])
       
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
