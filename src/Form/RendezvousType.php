<?php

namespace App\Form;

use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Rendezvous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezvousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('id')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('type_consultation_id')
            ->add('start_time', null, [
                'widget' => 'single_text',
            ])
            
            
            // ->add('PatientId', EntityType::class, [
            //     'class' => Patient::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('medecinId', EntityType::class, [
            //     'class' => Medecin::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendezvous::class,
        ]);
    }
}