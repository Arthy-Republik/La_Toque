<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
        /**
     * This controller allow us to display an ingredient
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    //injection de dépendance = injecter un service dans les parametres de la function ici ingredient repo qui se trouve dans le repo ingredient

    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),
            $request->query->getInt('page', 1), 
            10 /*limit per page*/
        );
        
        return $this->render('pages/ingredient/index.html.twig', [
          'ingredients'=> $ingredients
        ]);
    }
    /**
     * This controller allow us to create an ingredient
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { 
            $ingredient = $form ->getData();

            //on dit au manager de persister la donnée = elle doit s'ajouter à la BDD , le flush permet de l'enoyer en BDD
            $manager ->persist($ingredient);
            $manager->flush();

        //ajout de message flash 
          $this->addFlash(
              'success',
              'Votre ingrédient a été crée avec succés !'
          );

          return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to update an ingredient
     */
    #[Route('/ingredient/edition/{id}', name:'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { 
            $ingredient = $form ->getData();

            $manager ->persist($ingredient);
            $manager->flush();

        //ajout de message flash 
          $this->addFlash(
              'Success',
              'Votre ingrédient a été modifié avec succés !'
          );

          return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig', [ 
            'form' => $form->createView()
        ]);
    }
     /**
     * This controller allow us to delete an ingredient
     */
    #[Route('/ingredient/suppression/{id}', name:'ingredient.delete', methods:['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'votre ingrédient a été supprimé avec succés'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
