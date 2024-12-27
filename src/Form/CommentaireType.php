<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('texte', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => ['placeholder' => 'Écrivez votre commentaire...', 'rows' => 4]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter le commentaire',
                'attr' => ['class' => 'btn bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-500 transition']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
