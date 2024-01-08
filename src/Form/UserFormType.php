<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add('email', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
                'attr' => [
                    'readonly' => true,
                    'class' => 'readonly'
                ],
                'label' => $translator->trans('account.form.email', [], 'app')
            ])
            ->add('pictureProfile', VichImageType::class, [
                'required' => false,
                'mapped' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'label' => $translator->trans('account.form.pictureProfil', [], 'app'),
                'attr' => [
                    'class' => 'form_input_image',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => $translator->trans('account.error.pictureProfil.mimeTypes', [], 'app')
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translator' => null,
        ]);
    }
}
