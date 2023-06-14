<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('email',EmailType::class,
            array('constraints' => [new NotBlank()])
           )
           ->add('password',RepeatedType::class,
           array(
               'type'=>PasswordType::class,
               'first_options'=> array('label'=>'password'),
               'second_options' => array('label'=>'Repetir Password'),
               'constraints' => [new NotBlank()]
           ))
           ->add('nombreUsuario',TextType::class)
           ->add('localidad',TextType::class,
                array('constraints' => [new NotBlank()]))
           ->add('telefono',TextType::class,
               array('constraints' => [new NotBlank()]));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=> 'App\Entity\Usuario'
        ));
    }
}