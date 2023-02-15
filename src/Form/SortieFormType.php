<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateDeb= new \DateTime('19:00:00');
        $dateDeb->add(new \DateInterval('P2D'));
        $datelimite= new \DateTime('18:00:00');
        $datelimite->add(new \DateInterval('P1D'));

        $builder
            ->add('nom')
            ->add('debutSortie',DateTimeType::class, [
        'data' => $dateDeb,
    ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                'data' => $datelimite,
            ])
            ->add('nombreInscriptionsMax',null,['attr' => ['value'=>5]])
            ->add('infosSortie')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un campus',
                'required' => false,
            ])
            ->add('Envoyer', SubmitType::class);
        ;
    }

    //                [   "class"=>User::class,
//                    "choice_label"=>"username",
////                    'attr' => ['readonly' => true]
////
//                    'disabled' => 'true'
//                ]

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
