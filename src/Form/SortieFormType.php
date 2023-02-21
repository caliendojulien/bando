<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateDeb= new \DateTime('19:00:00');
        $dateDeb->add(new \DateInterval('P2D'));
        $datelimite= new \DateTime('18:00:00');
        $datelimite->add(new \DateInterval('P1D'));
        $today= new \DateTime('now');
        $todayPlus3h = $today->add(new \DateInterval('PT3H'));
        $todayPlus4h = $today->add(new \DateInterval('PT4H'));

        $builder
            ->add('nom',null,['attr' => ['class' => 'input-field inline'],])
            ->add('debutSortie',DateTimeType::class,
                [
                'required' => true,
                    'invalid_message' => 'La sortie ne peut pas être antérieure à aujourdhui.',
                'data' => $dateDeb,
                    'constraints' => [
                        new GreaterThan([
                            'value' => $todayPlus4h,
                            'message' => "la sortie doit démarrer au plus tôt dans 3 heures."
                        ])]
                ]
            )
            ->add('dateLimiteInscription',DateTimeType::class, [
                'required' => true,
                'invalid_message' => 'La date limite ne peut pas être antérieure à aujourdhui.',
                'data' => $datelimite,
                'constraints' => [
                    new GreaterThan([
                        'value' => $todayPlus3h,
                        'message' => "La date d'inscription est illogique."
                    ]),
//Todo faire marcher ça...

//                    new LessThan([
//                        'propertyPath' => 'debutSortie',
//                        'message' => "La date d'inscription doit être antérieure à la date de début de sortie."
//                    ])
                ]
            ])
            ->add('nombreInscriptionsMax',null,
                ['attr' => ['value'=>5],
                'constraints' => [ new GreaterThan([  'value' => 0 ]),
                                    new LessThan([  'value' => 1001])
                                ]
                ]
            )
            ->add('infosSortie',null,[
//                    'placeholder' => 'Vous pouvez décrire la sortie si vous voulez...',
                        'required' => false,])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un campus',
                'required' => false,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
