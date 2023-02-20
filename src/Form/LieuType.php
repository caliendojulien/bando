<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('rue')
            ->add('latitude', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex('/^-?\d+\.\d+$/'),
                ],
            ])
            ->add('longitude', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex('/^-?\d+\.\d+$/'),
                ],
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,

                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner une ville',
                'required' => true,
            ])
            ->add('creer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
