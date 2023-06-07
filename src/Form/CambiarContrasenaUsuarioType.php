<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class CambiarContrasenaUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Contraseña actual',
                'attr' => [
                    'autocomplete' => false,
                    'readonly' => 'readonly',
                    'onfocus' => 'this.removeAttribute(\'readonly\');'
                ],
                'constraints' => [
                    new UserPassword(),
                    new NotBlank()
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las claves no coinciden',
                'first_options' => [
                    'label' => 'Nueva contraseña',
                    'attr' => [
                        'autocomplete' => false,
                        'readonly' => 'readonly',
                        'onfocus' => 'this.removeAttribute(\'readonly\');'
                    ]
                ],
                'second_options' => [
                    'label' => 'Repita contraseña',
                    'attr' => [
                        'autocomplete' => false,
                        'readonly' => 'readonly',
                        'onfocus' => 'this.removeAttribute(\'readonly\');'
                    ]
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'=> null
        ]);
    }
}