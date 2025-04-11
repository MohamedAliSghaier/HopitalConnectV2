<?php


// src/Form/AvisType.php
class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', IntegerType::class)
            ->add('comment', TextareaType::class)
            ->add('submit', SubmitType::class);
    }
}
