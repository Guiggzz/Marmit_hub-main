<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la recette',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom pour la recette']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de la recette',
                ],
            ])
            ->add('texte', TextareaType::class, [
                'label' => 'Instructions de préparation',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir les instructions de préparation']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Expliquer la préparation',
                    'rows' => 5,
                ],
            ])
            ->add('duree_totale', IntegerType::class, [
                'label' => 'Durée totale (en minutes)',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir la durée de préparation']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Durée en minutes',
                ],
            ])
            ->add('nombre_personnes', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer le nombre de personnes']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre de personnes',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de la recette',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control-file',
                ],
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // Affichage sous forme de cases à cocher
                'mapped' => false, // Les ingrédients ne sont pas directement liés à Recette
            ])
            ->add('recetteIngredients', CollectionType::class, [
                'entry_type' => RecetteIngredientType::class, // Formulaire pour chaque association ingrédient/quantité
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true, // Permet d'ajouter dynamiquement des champs
                'by_reference' => false, // Pour permettre la gestion de la collection
                'mapped' => false, // Les ingrédients ne sont pas directement liés à Recette

            ]);
        // ->add('recetteIngredients', CollectionType::class, [
        //     'entry_type' => RecetteIngredientType::class,
        //     'allow_add' => true,
        //     'by_reference' => false,
        //     'label' => 'Ingrédients',
        //     'mapped' => false, // Les ingrédients ne sont pas directement liés à Recette

        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
