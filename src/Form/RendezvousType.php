<?php

namespace App\Form;

use App\Entity\Rendezvous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Medecin;
use \DateTime;


class RendezvousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date', DateType::class, [
            'widget' => 'single_text',
            'attr' => [
                'min' => (new \DateTime())->format('Y-m-d'), // autorise aujourd'hui
                'class' => 'form-control flatpickr-date',
            ],
            'label' => 'Date du rendez-vous',
        ])
        
            ->add('typeConsultationId', ChoiceType::class, [
                'choices' => [
                    'Consultation' => 1,
                    'Téléconsultation' => 2,
                ],
                'multiple' => false,
                'label' => 'Type de Consultation',
                'data' => 1, // Sélection par défaut : Consultation
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('startTime', ChoiceType::class, [
                'label' => 'Heure de début',
                'placeholder' => 'Choisissez une heure',
                'choices' => $this->getAvailableHours(),
                'choice_label' => function ($choice, $key, $value) {
                    return $key;
                },
                'choice_value' => function (?DateTime $dateTime) {
                    return $dateTime ? $dateTime->format('H:i') : '';
                },
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => function (Medecin $medecin) {
                    $utilisateur = $medecin->getUtilisateur(); // c'est un Utilisateur
                    return 'Dr ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom() . ' - ' . $medecin->getSpecialite();
                },
                'label' => 'Médecin',
                'placeholder' => 'Choisissez un médecin',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendezvous::class,
        ]);
    }
    private function getAvailableHours(): array
{
    $hours = [];
    for ($i = 8; $i <= 17; $i++) {
        $label = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        $hours[$label] = new \DateTime($label);
    }
    return $hours;
}

}
