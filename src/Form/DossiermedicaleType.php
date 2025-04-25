<?php

namespace App\Form;

use App\Entity\Dossiermedicale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DossiermedicaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taille', NumberType::class, [
                'label' => 'Taille (cm)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: 170'],
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids (kg)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: 70'],
            ])
            ->add('maladies', ChoiceType::class, [
                'label' => 'Avez-vous des maladies ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('maladies_details', TextType::class, [
                'label' => 'Détails des maladies',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Diabète, Hypertension'],
            ])
            ->add('antecedents_cardiovasculaires_familiaux', ChoiceType::class, [
                'label' => 'Antécédents cardiovasculaires familiaux',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                    'Je ne sais pas' => 'Je ne sais pas',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('antecedents_cardiovasculaires_familiaux_details', TextType::class, [
                'label' => 'Détails des antécédents',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Antécédents familiaux de crise cardiaque'],
            ])
            ->add('asthmatique', ChoiceType::class, [
                'label' => 'Asthmatique',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('asthmatique_details', TextType::class, [
                'label' => 'Détails sur l\'asthme',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Asthme sévère'],
            ])
            ->add('suivi_dentaire_regulier', ChoiceType::class, [
                'label' => 'Suivi dentaire régulier',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                    'Je ne sais pas' => 'Je ne sais pas',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('suivi_dentaire_regulier_details', TextType::class, [
                'label' => 'Détails du suivi dentaire',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Consultation annuelle'],
            ])
            ->add('antecedents_chirurgicaux', ChoiceType::class, [
                'label' => 'Antécédents chirurgicaux',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                    'Je ne sais pas' => 'Je ne sais pas',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('antecedents_chirurgicaux_details', TextType::class, [
                'label' => 'Détails des antécédents chirurgicaux',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Appendicectomie'],
            ])
            ->add('allergies', ChoiceType::class, [
                'label' => 'Avez-vous des allergies ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('allergies_details', TextType::class, [
                'label' => 'Détails des allergies',
                'required' => false,
                'attr' => ['class' => 'details-field', 'placeholder' => 'Ex: Allergie aux arachides'],
            ])
            ->add('profession', TextType::class, [
                'label' => 'Profession',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Enseignant'],
            ])
            ->add('niveau_de_stress', ChoiceType::class, [
                'label' => 'Niveau de stress',
                'choices' => [
                    'Faible' => 'Faible',
                    'Moyen' => 'Moyen',
                    'Élevé' => 'Élevé',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('qualite_de_sommeil', ChoiceType::class, [
                'label' => 'Qualité de sommeil',
                'choices' => [
                    'Mauvaise' => 'Mauvaise',
                    'Moyenne' => 'Moyenne',
                    'Bonne' => 'Bonne',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('activite_physique', ChoiceType::class, [
                'label' => 'Activité physique',
                'choices' => [
                    'Marche' => 'Marche',
                    'Sport' => 'Sport',
                    'Autre' => 'Autre',
                    'Aucune' => 'Aucune',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ])
            ->add('situation_familiale', ChoiceType::class, [
                'label' => 'Situation familiale',
                'choices' => [
                    'Célibataire' => 'Célibataire',
                    'Marié' => 'Marié',
                    'Divorcé' => 'Divorcé',
                    'Veuf' => 'Veuf',
                ],
                'placeholder' => 'Sélectionnez une option',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossiermedicale::class,
        ]);
    }
}