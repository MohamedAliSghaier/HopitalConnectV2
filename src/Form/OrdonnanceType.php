<?php

namespace App\Form;

use App\Entity\Ordonnance;
use App\Entity\Medecin;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdonnanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medecin_id', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => 'nom', // Adjust based on your Medecin entity
                'label' => 'Médecin',
            ])
            ->add('patient_id', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => 'nom', // Adjust based on your Patient entity
                'label' => 'Patient',
            ])
            ->add('medicaments', TextareaType::class, [
                'label' => 'Médicaments',
            ])
            ->add('date_prescription', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de Prescription',
                'required' => false,
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
            ])
            ->add('statut', TextType::class, [
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
