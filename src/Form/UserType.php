<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("username", TextType::class, [
            "attr" => ["class" => "form-control"],
            "label" => "Nom d'utilisateur", 
            "label_attr" => ["class" => "form-label fw-bold"],
            "required" => true
        ]);

        $builder->add("password", RepeatedType::class, [
            "first_options" => [
                "label" => "Mot de passe", 
                "label_attr" => [
                    "class" => "form-label fw-bold"
                ],
                "row_attr" => ["class" => "mt-2"]
            ],
            "invalid_message" => "Les mots de passes ne correspondent pas.",
            "options" => [
                "attr" => ["class" => "form-control"]
            ],
            "required" => true,
            "second_options" => [
                "label" => "Tapez le mot de passe à nouveau", 
                "label_attr" => ["class" => "form-label fw-bold"],
                "row_attr" => ["class" => "mt-2"]
            ],
            "type" => PasswordType::class
        ]);

        $builder->add("email", EmailType::class, [
            "attr" => ["class" => "form-control"],
            "label" => "Adresse email", 
            "label_attr" => ["class" => "form-label fw-bold"],
            "required" => true,
            "row_attr" => ["class" => "mt-2"]
        ]);

        $builder->add("role", ChoiceType::class, [
            "attr" => ["class" => "form-select"],
            "choices"  => [
                "Utilisateur" => "ROLE_USER",
                "Administrateur" => "ROLE_ADMIN"
            ],
            "data" => $options["data"]->getRoles()[0] ?? "ROLE_USER",
            "label" => "Rôle", 
            "label_attr" => ["class" => "form-label fw-bold"],
            "mapped" => false,
            "row_attr" => ["class" => "mt-2"],
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
            "data_class" => User::class,
        ]);
    }
}
