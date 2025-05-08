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
use Symfony\Component\Validator\Constraints\NotBlank;



class RendezvousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'class' => 'form-control flatpickr-date',
                ],
                'label' => 'Date du rendez-vous',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une date.']),
                ],
            ])
            ->add('type_consultation_id', ChoiceType::class, [
                'choices' => [
                    'Consultation' => 1,
                    'Téléconsultation' => 2,
                ],
                'multiple' => false,
                'label' => 'Type de Consultation',
                'data' => 1,
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type de consultation.']),
                ],
            ])
            ->add('start_time', ChoiceType::class, [
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
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une heure.']),
                ],
            ])
            ->add('medecinId', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => function (Medecin $medecin) {
                    $utilisateur = $medecin->getId();
                    return 'Dr ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom() . ' - ' . $medecin->getSpecialite();
                },
                'label' => 'Médecin',
                'placeholder' => 'Choisissez un médecin',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un médecin.']),
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
