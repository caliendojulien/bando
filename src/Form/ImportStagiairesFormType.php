<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ImportStagiairesFormType extends AbstractType
{
    // ...

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier', FileType::class, [
                'label' => 'Fichier',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Callback([
                        'callback' => [$this, 'validateFichier'],
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Importer',
                'attr' => ['class' => 'btn-primary'],
            ]);
    }

    public function validateFichier($value, ExecutionContextInterface $context)
    {
        $extensionsAutorisees = ['csv', 'xls', 'xlsx', 'ods'];
        $extensionFichier = $value->getClientOriginalExtension();

        if (!in_array($extensionFichier, $extensionsAutorisees)) {
            $context->addViolation('Le format du fichier est invalide. Formats autorisés : ' . implode(', ', $extensionsAutorisees));
        }
    }
}


