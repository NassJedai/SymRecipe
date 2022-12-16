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


    /*
     * This function display all ingredients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredient = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1) ,
            10
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredient
        ]);
    }

    #[Route ('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
     public function new(
         Request $request,
         EntityManagerInterface $manager
    ): Response

     {
         $ingredient = new Ingredient();
         $form = $this->createForm(IngredientType::class, $ingredient);

         $form->handleRequest($request);
         if ($form ->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush($ingredient);

             $this->addFlash(
                 'success',
                 'Votre ingrédient a été créé avec succès !'
             );

            // Problem with Route
            //$this->redirectToRoute('ingredient.index');

         } else {
             $this->addFlash(
                 'danger',
                 "Votre ingrédient n'a pas été enregistré  !"
             );
         }

        return $this->render('ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
     }
}
