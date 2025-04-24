<?php

// src/Form/RegistrationType.php

// src/Form/RegistrationType.php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('plaintext', PasswordType::class, [
                'mapped' => false // Important car le mot de passe doit être traité séparément
            ])
            ->add('genre')
            ->add('photo', FileType::class, [
                'label' => 'Votre photo',
                'mapped' => false,
                'required' => false,
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'mapped' => false, // Important car ce champ n'existe pas dans Utilisateur
                'required' => true // Assurez-vous que la date est obligatoire
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'mapped' => false, // Important car ce champ n'existe pas dans Utilisateur
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
