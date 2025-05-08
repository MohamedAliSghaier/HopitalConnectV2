<?php

namespace App\Form;

use App\Entity\Analyse;
use App\Entity\Rendezvous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('id')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('type')
            ->add('RendezVous', EntityType::class, [
                'class' => RendezVous::class,
                'choice_label' => 'id',
            ])
            ->add('RendezVous', EntityType::class, [
                'class' => Rendezvous::class,
                'choices' => $options['rendezvous_choices'], // Use the filtered rendezvous list
                'choice_label' => function (Rendezvous $rendezvous) {
                    $patient = $rendezvous->getPatient();
                    $utilisateur = $patient ? $patient->getUtilisateur() : null;
                    return $utilisateur
                        ? $utilisateur->getNom() . ' ' . $utilisateur->getPrenom() . ' - ' . $rendezvous->getDate()->format('Y-m-d')
                        : 'Unknown Patient';
                },
                'label' => 'Select Patient (RendezVous)',
            ]);
            // ->add('id_patient', EntityType::class, [
            //     'class' => Rendezvous::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('id_medecin', EntityType::class, [
            //     'class' => Rendezvous::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('id_rendezvous', EntityType::class, [
            //     'class' => Rendezvous::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Analyse::class,
            'rendezvous_choices' => [],
        ]);
    }
}
