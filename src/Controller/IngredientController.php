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
     * This controller display all ingredients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     **/
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredient = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredient
        ]);
    }


    /**
     * This controller show form which create an ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route ('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $manager
    ): Response

    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush($ingredient);

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');

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




    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        Ingredient             $ingredient,
        Request                $request,
        EntityManagerInterface $manager): Response

    {
        //dd($ingredient);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush($ingredient);

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié  avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        if(!$ingredient) {

            $this->addFlash(
                'warning',
                "l'ingrédient en question n'a pas été trouvé "
            );
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre ingrédient a été supprimé avec succès"
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
