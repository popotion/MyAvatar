<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* @var TranslatorInterface $translator **/
        $translator = $options['translator'];

        $builder
            ->add('email', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank([
                        'message' => $translator->trans('security.register.error.emailNotBlank', [], 'app'),
                    ]),
                ]
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank([
                        'message' => $translator->trans('security.register.error.firstNameNotBlank', [], 'app'),
                    ]),
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank([
                        'message' => $translator->trans('security.register.error.lastNameNotBlank', [], 'app'),
                    ]),
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => $translator->trans('security.register.error.firstNameNotBlank', [], 'app'),
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('security.register.error.passwordNotBlank', [], 'app'),
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('repeatPlainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('security.register.error.passwordNotBlank', [], 'app'),
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                    ]),
                ],
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
