<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Medecin;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $medecins = $options['medecins'];

        $builder
            // Add the 'Medecin' field as a dropdown, only showing Medecins with whom the patient had a rendezvous
            ->add('medecin', ChoiceType::class, [
                'choices' => $this->getMedecinChoices($medecins),
                'placeholder' => 'Sélectionnez un médecin',
                'label' => 'Médecin',
                'required' => true,
            ])
        
         
            ->add('commentaire')
            ->add('note');
    }

    private function getMedecinChoices(array $medecins): array
    {
        $choices = [];
        foreach ($medecins as $medecin) {
            $choices[$medecin->getUtilisateur()->getNom()] = $medecin; // Assuming getNom() and getId() are methods on Medecin
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
            'medecins' => [], // default value for dynamic list

        ]);
    }
}