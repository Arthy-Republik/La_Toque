<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        /**
         * This controller dislay all recipes
         */
        {
            $recipes = $paginator->paginate(
                $recipeRepository->findAll(),
                $request->query->getInt('page', 1), 
                10 /*limit per page*/
            );

                return $this->render('pages/recipe/index.html.twig', [
                    'recipes' => $recipes,
            ]);
        }
    }

    /**
    * This controller allow us to creat a new recipe
    */
    #[Route('/recette/creation', name: 'recipe.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
           
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { 
            
            $recipe = $form ->getData();

            $manager->persist($recipe);
            $manager->flush();
           
            $this->addFlash(
                'success',
                'Votre recette a été crée avec succés'
            );
            
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [ 
            'form' => $form->createView()
        ]);
    }

    /**
   * This controller allow us to edit a recipe
   */
    #[Route('/recette/edition/{id}', name:'recipe.edit', methods:['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe = $form ->getData();

            $manager ->persist($recipe);
            $manager->flush();

                //ajout de message flash 
          $this->addFlash(
            'Success',
            'Votre recette a été modifié avec succés !'
        );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', [ 
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to delete a recipe
     */
    #[Route('/recette/suppression/{id}', name:'recipe.delete', methods:['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager) : Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'votre recette a été supprimée avec succés'
        );
        return $this->redirectToRoute('recipe.index');
    }
}