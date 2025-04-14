<?php

namespace App\Form;

use App\Entity\Medicament;
use App\Entity\Pharmacien;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MedicamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('id')
            ->add('nom')
            ->add('stock');
            //->add('pharmacien', EntityType::class, [
                //'class' => Pharmacien::class,
                //'choice_label' => 'nom', // ou 'email', selon ce que tu veux afficher
           // ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medicament::class,
        ]);
    }
}
