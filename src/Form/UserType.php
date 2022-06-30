<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add ('fullName', TextType::class, [ 
            'attr' => [
                'class' => 'form-control',
                'minlength' => '2',
                'maxlength' => '50'
            ],
                'label' => 'Nom / Prénom',
                'label_attr' => [ 
                'class' => 'form_label' 
            ],
                'constraints' => [ 
                new Assert\NotBlank(),
                new Assert\Length(['min' => 2, 'max' => 50]),
            ]
        ])
        ->add('pseudo', TextType::class, [ 
            'attr' => [
                'class' => 'form-control',
                'minlength' => '2',
                'maxlength' => '50',
            ],
                'required' => false,
                'label' => 'Pseudo (Facultatif)',
                'label_attr' => [ 
                'class' => 'form_label' 
            ],
                'constraints' => [ 
                new Assert\Length(['min' => 2, 'max' => 50]),
            ]
        ])
        ->add('Modifier', SubmitType::class, [ 
            'attr' => [ 
                'class' => 'form-btn-edit'
            ]
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
