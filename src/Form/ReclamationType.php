<?php


// src/Form/ReclamationType.php
class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('issue_description', TextareaType::class)
            ->add('submit', SubmitType::class);
    }
}
