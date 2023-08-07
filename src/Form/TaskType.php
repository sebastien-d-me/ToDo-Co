<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("title", TextType::class, [
            "attr" => ["class" => "form-control"],
            "label" => "Titre", 
            "label_attr" => ["class" => "form-label fw-bold"],
            "required" => true,
            "row_attr" => ["class" => "mt-2"]
        ]);

        $builder->add("content", TextareaType::class, [
            "attr" => ["class" => "form-control", "rows" => "5"],
            "label" => "Contenu", 
            "label_attr" => ["class" => "form-label fw-bold"],
            "required" => true,
            "row_attr" => ["class" => "mt-2"]
        ]);

        $builder->add("save", SubmitType::class, [
            "attr" => ["class" => "btn btn-success"],
            "label" => "Sauvegarder",
            "row_attr" => ["class" => "d-flex justify-content-end mt-3"]
        ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Task::class,
        ]);
    }
}
