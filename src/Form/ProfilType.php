<?php

namespace App\Form;

use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', null,
                [
                    'disabled' => true,
                    'attr' => ['class' => 'profilPrenom', 'pattern' => '^[^@&"()!_$*€£`+=\/;?#]+$', 'maxlength' => 150],
                ])
            ->add('nom', null,
                [
                    'disabled' => true,
                    'attr' => ['class' => 'profilNom', 'pattern' => '^[^@&"()!_$*€£`+=\/;?#]+$', 'maxlength' => 150],
                ])
            ->add('telephone', TelType::class,
                [
                    'required' => true,
                    'attr' => ['class' => 'profilPrenom', 'pattern' => '(0|\+33)[1-9]( *[0-9]{2}){4}', 'maxlength' => 10]
                ])
            ->add('email', EmailType::class,
                [
                    'required' => true,
                    'attr' => ['class' => 'profilEmail', 'maxlength' => 255],
                ])
            ->add('password', PasswordType::class,
                ['mapped' => false, 'required' => true, 'label' => 'Mot de passe', 'hash_property_path' => 'password'],
            )
            ->add('campus', null, [
                'attr' => ['class' => 'profilCampus'],
            ])
            ->add('url_photo', HiddenType::class,
                [
                    'attr' => ['visible' => 'hidden'],
                ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_label' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Le fichier est trop grand',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Votre photo doit être au format .jpg ou .jpeg ou .png'
                    ])
                ]
            ])
            ->add('Envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
