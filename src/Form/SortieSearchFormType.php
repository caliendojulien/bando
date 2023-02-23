<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('get')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les campus',
                'required' => false,
            ])
            ->add('nom', null,['required' => false])
            ->add('debutSortie',DateType::class, [
                'widget'=>'single_text',
                'data' => (new \DateTime('00:00:01'))->modify('-12 month'),
            ])
            ->add('finSortie', DateType::class, [
                'widget'=>'single_text',
                'data' => (new \DateTime('23:59:59'))->modify('+12 month'),
            ])
            ->add('organisateur', CheckboxType::class, [
                'required'  => false,
                'label'     => "Sorties dont je suis l'organisateur/trice",
            ])
            ->add('inscrit', CheckboxType::class, [
                'required'  => false,
                'label'     => "Sorties auxquelles je suis inscrit/e",
            ])
            ->add('sorties_ouvertes', CheckboxType::class, [
                'required'  => false,
                'label'     => "Sorties ouvertes",
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => Sortie::class,
        ]);
    }
}
