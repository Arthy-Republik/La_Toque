<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;


class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add ('name', TextType::class, [ 
                'attr' => [ 
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ], 
                    'label' => 'Nom',
                    'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                  'constraints' => [ 
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
            ]
            ])
            ->add('time', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 1440
                ],
                'label' => 'Temps (en minutes)',
                'label_attr' => [ 
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1441)
                ]
            ])
            ->add('nbPeople', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 50
                ],
                'label' => 'Nombre de personnes',
                'label_attr' => [ 
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(51)
                ]
            ])
            ->add('difficulty', RangeType::class, [
                    'attr' => [
                    'class' => 'form-range',
                    'min' => 1,
                    'max' => 5
                    ],
                    'label' => 'Difficulté',
                    'label_attr' => [ 
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(5)
                    ]
            ])
            ->add('description', TextareaType::class, [ 
                    'attr' => [
                    'class' => 'form-range',
                    'min' => 1,
                    'max' => 50
                    ],
                    'label' => 'Description',
                    'label_attr' => [ 
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\NotBlank()
                    ]
            ])
            ->add('price', MoneyType::class, [
                'attr' => [ 
                    'class' => 'form-control',
              ],
                'label' => 'Prix ',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                    new Assert\LessThan(1001)
                ]
            ])
             ->add('isFavorite', CheckboxType::class,[ 
                'attr' => [ 
                    'class' => 'form-control',
              ],
                'required' => false,
                'label' => 'Favoris ? ',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                   new Assert\NotNull()
                   
                ]
             ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'query_builder' => function (IngredientRepository $ingredientRepository) {
                    return $ingredientRepository -> createQueryBuilder('i')
                    ->orderBy('i.name','ASC');
                },
                'label' => 'les ingrédients',
                'label_attr' => [ 
                    'class' => 'form-control mt-4'
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class)
        ;
    
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
