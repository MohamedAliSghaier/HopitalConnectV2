<?php

namespace App\Form;

use App\Entity\Assurance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class AssuranceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomAssureur', ChoiceType::class, [
                'label' => 'Nom de l\'assureur',
                'choices' => [
                    'COMAR Assurance' => 'COMAR Assurance',
                    'CNAM Assurances' => 'CNAM Assurances',
                    'GAT Assurances' => 'GAT Assurances',
                    'CARTE Assurances' => 'CARTE Assurances',
                    'BH Assurances' => 'BH Assurances',
                    'Zitouna Takaful Assurances' => 'Zitouna Takaful Assurances',
                ],
                'placeholder' => 'Sélectionnez une option',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('TypeAssureur', ChoiceType::class, [
                'label' => 'Type d\'assureur',
                'choices' => [
                    'Public' => 'Public',
                    'Privé' => 'Privé',
                ],
                'placeholder' => 'Sélectionnez une option',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('NumeroPolice', IntegerType::class, [
                'label' => 'Numéro de police',
                'attr' => [
                    'maxlength' => 8, // Enforce max length on the frontend
                    'minlength' => 8, // Enforce min length on the frontend
                    'pattern' => '\d{8}', // Ensure only 8 digits are allowed
                    'placeholder' => 'Ex: 12345678',
                ],
                'required' => true,
            ])
            ->add('NomTitulaire', TextType::class, [
                'label' => 'Nom du titulaire',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('TypeCouverture', ChoiceType::class, [
                'label' => 'Type de couverture',
                'choices' => [
                    'Hospitalisation' => 'Hospitalisation',
                    'Soins ambulatoires' => 'Soins ambulatoires',
                    'Urgences' => 'Urgences',
                    'Médicaments' => 'Médicaments',
                    'Maternité' => 'Maternité',
                    'Psychiatrie' => 'Psychiatrie',
                    'Dentaire/Ophtalmologie' => 'Dentaire/Ophtalmologie',
                ],
                'placeholder' => 'Sélectionnez une option',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control js-datepicker'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control js-datepicker'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assurance::class,
        ]);
    }
}