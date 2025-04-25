<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class OrdonnanceMedicamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du médicament',
                'attr' => ['placeholder' => 'Paracetamol'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du médicament est obligatoire.']),
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['placeholder' => '4'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\Positive(['message' => 'La quantité doit être un nombre positif.']),
                ],
            ]);
    }
}