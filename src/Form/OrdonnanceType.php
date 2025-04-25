<?php

namespace App\Form;

use App\Entity\Ordonnance;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\OrdonnanceMedicamentType; // Import the new form type

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
                'entry_type' => OrdonnanceMedicamentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('date_prescription', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date de Prescription',
                'attr' => ['class' => 'form-control js-datepicker'],
            ])

            ->add('instructions', TextType::class, [
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
