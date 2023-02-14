<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('debutSortie')
            //->add('finSortie')
            ->add('dateLimiteInscription')
            ->add('nombreInscriptionsMax')
            ->add('infosSortie')
            //->add('motifAnnulation')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un campus',
                'required' => false,
            ])
//            ->add('lieu', EntityType::class, [
//                'class' => Lieu::class,
//                'choice_label' => 'nom',
//                'placeholder' => 'Sélectionner un lieu',
//                'required' => false,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
