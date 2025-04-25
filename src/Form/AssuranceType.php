<?php

namespace App\Form;

use App\Entity\Assurance;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AssuranceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control js-datepicker'],
                'format' => 'yyyy-MM-dd',
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'End Date',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control js-datepicker'],
                'format' => 'yyyy-MM-dd',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assurance::class,
        ]);
    }
}