<?php

namespace App\Form;

use App\Entity\Ordonnance;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdonnanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => 'nom',
                'label' => 'Patient',
            ])
            ->add('medicaments', CollectionType::class, [
                'entry_type' => TextareaType::class, // Use TextareaType for each medicament
                'entry_options' => ['label' => 'Médicament (nom:quantité)'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ])
            ->add('date_prescription', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de Prescription',
                'required' => false,
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Terminée' => 'Terminée',
                    'Annulée' => 'Annulée',
                ],
                'label' => 'Statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ordonnance::class,
        ]);
    }
}
