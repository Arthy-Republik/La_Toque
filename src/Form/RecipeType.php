<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;


class RecipeType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add ('name', TextType::class, [ 
                'attr' => [ 
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ], 
                    'label' => 'Nom de la recette ',
                    'label_attr' => [
                    'class' => 'form-label'
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
                'label' => 'Temps (en minutes) ',
                'label_attr' => [ 
                    'class' => 'form-label '
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
                'label' => 'Nombre de personne(s) ',
                'label_attr' => [ 
                    'class' => 'form-label'
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
                    'label' => 'Difficulté /5',
                    'label_attr' => [ 
                        'class' => 'form-label'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(6)
                    ]
            ])
            ->add('description', TextareaType::class, [ 
                    'attr' => [
                    'class' => 'form-range',
                    'min' => 1,
                    'max' => 50
                    ],
                    'label' => 'Description de la recette  ',
                    'label_attr' => [ 
                        'class' => 'form-label'
                    ],
                    'constraints' => [
                        new Assert\NotBlank()
                    ]
            ])
            ->add('price', MoneyType::class, [
                'attr' => [ 
                    'class' => 'form-control',
              ],
                'label' => ' Prix 
                en
                ',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                    new Assert\LessThan(1001)
                ]
            ])
             ->add('isFavorite', CheckboxType::class,[ 
                'attr' => [ 
                    'class' => 'form-check-input',
              ],
                'required' => false,
                'label' => 'Voulez vous la mettre en favorite ? ',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [ 
                    new Assert\NotNull()
                ]
             ])
             ->add('isPublic', CheckboxType::class,[ 
                'attr' => [ 
                    'class' => 'form-check-input-public',
              ],
                'required' => false,
                'label' => 'Voulez vous que votre recette soit partagé avec la communauté La Toque ? ',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [ 
                    new Assert\NotNull()
                ]
             ])
           ->add('imageFile', VichImageType::class, [ 
               'label' => 'Image de la recette',
               'label_attr' => [
                   'class' => 'form-label '
               ]
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'query_builder' => function (IngredientRepository $ingredientRepository) {
                    return $ingredientRepository -> createQueryBuilder('i')
                    ->where('i.user = :user')
                    ->orderBy('i.name','ASC')
                    ->setParameter('user', $this->token->getToken()->getUser());
                },
                'label' => 'Les ingrédients',
                'label_attr' => [ 
                    'class' => 'ingredientBox '
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-NewRecipeForm'
            ],
                'label' => 'Créer ma recette'
            ])
        ;
    
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
