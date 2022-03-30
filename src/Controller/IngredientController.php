<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'ingredient', methods: ['GET'])]
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
}
