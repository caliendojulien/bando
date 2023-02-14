<?php

namespace App\Form;

use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('prenom', null, ['attr' => ['class' => 'profilPrenom'],])
            ->add('nom', null, ['attr' => ['class' => 'profilNom'],])
            ->add('telephone', TelType::class, ['attr' => ['class' => 'profilPrenom'],])
            ->add('email', EmailType::class, ['attr' => ['class' => 'profilEmail'],])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe', 'hash_property_path' => 'password'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'mapped' => false,
            ])
            ->add('campus', null, ['attr' => ['class' => 'profilCampus'],])
            ->add('url_photo', HiddenType::class, ['attr' => ['visible' => 'hidden'],])
            ->add('photo_download', FileType::class, ['mapped' => false])
            ->add('Envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
