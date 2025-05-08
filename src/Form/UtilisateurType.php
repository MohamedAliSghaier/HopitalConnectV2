<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'Le nom est obligatoire.']),
                new Length(['max' => 50, 'maxMessage' => 'Le nom ne peut pas dépasser 50 caractères.']),
            ],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'Le prénom est obligatoire.']),
                new Length(['max' => 50, 'maxMessage' => 'Le prénom ne peut pas dépasser 50 caractères.']),
            ],
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'L\'email est obligatoire.']),
            ],
        ])
        ->add('role', ChoiceType::class, [
            'label' => 'Rôle',
            'choices' => [
                'Médecin' => 'medecin',
                'Pharmacien' => 'pharmacien',
                'Administrateur' => 'administrateur',
                'Patient' => 'patient',
            ],
            'attr' => ['class' => 'form-select role-selector']
        ])
        ->add('specialite', TextType::class, [
            'label' => 'Spécialité',
            'required' => false,
            'mapped' => false,
            'attr' => ['class' => 'form-control specialite-field']
        ])
        ->add('num_rdv_max', IntegerType::class, [
            'label' => 'Nombre max de RDV',
            'required' => false,
            'mapped' => false,
            'attr' => ['class' => 'form-control num-rdv-max-field']
        ])
        ->add('genre', ChoiceType::class, [
            'label' => 'Genre',
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ],
            'attr' => ['class' => 'form-select'],
            'placeholder' => 'Sélectionnez le genre',
        ])
        ->add('photo', FileType::class, [
            'label' => 'Photo de profil',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                ]),
            ],
            'attr' => ['class' => 'form-control'],
        ]);

    if ($options['with_password']) {
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'options' => ['attr' => ['class' => 'form-control']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmer le mot de passe'],
            'mapped' => false,
            
                
            
        ]);
    }
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'is_new' => true, 
            'with_password' => true, 
        ]);
    }
}