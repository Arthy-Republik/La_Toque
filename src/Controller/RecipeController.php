<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This controller dislay all recipes
     */

  
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        {
            $recipes = $paginator->paginate(
                $recipeRepository->findBy(['user' => $this->getUSer()]),
                $request->query->getInt('page', 1), 
                10 /*limit per page*/
            );

                return $this->render('pages/recipe/index.html.twig', [
                    'recipes' => $recipes,
            ]);
        }
    }
  /**
     * This controller display all the public recipes 
     */
    
    #[Route('/recette/publique', 'recipe.public', methods: ['GET'])]
    public function public(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request ) : Response 
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findPublicRecipe(50),
            $request->query->getInt('page', 1), 
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/public.html.twig', [ 
            'recipes' => $recipes
        ]);
    }
    /**
    * This controller allow us to create a new recipe
    */
   
    #[Route('/recette/creation', name:'recipe.new', methods:['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
      
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { 
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();
           
            $this->addFlash(
                'success',
                'Votre recette a ??t?? cr??e avec succ??s'
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [ 
            'form' => $form->createView()
        ]);
    }
    /**
    * This controller allow us to see the description of a recipe but only for the specific user
    */
    
    #[Security("is_granted('ROLE_USER') and (recipe.getIsPublic() === true || user === recipe.getUser())")]
    #[Route('/recette/{id}', name:'recipe.show', methods: ['GET'])]
    public function show(Recipe $recipe) : Response
    {
        return $this->render('pages/recipe/show.html.twig', [ 
            'recipe' => $recipe
        ]);
    }

    /**
   * This controller allow us to edit a recipe
   */

   // j'autorise uniquement l'acc??s a cette page si l'utilisateur courant est l'utilisateur qui correspond ?? la recette
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
            'Votre recette a ??t?? modifi?? avec succ??s !'
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
    
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/suppression/{id}', name:'recipe.delete', methods:['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager) : Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'votre recette a ??t?? supprim??e avec succ??s'
        );
        return $this->redirectToRoute('recipe.index');
    }
}