<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategorieRepository $categorieRepository, ProgramRepository $programRepository)
    {
        $categorie = $categorieRepository->findByName($categoryName);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'Categorie "' . $categoryName . '" does not exist in Database'
            );
        }

        $categoryResults = $programRepository->findByCategorie(
            $categorie,
            ['id' => 'ASC']
        );


        return $this->render('category/show.html.twig', [
            'categoryResults' => $categoryResults,
            'category' => $categoryName
        ]);
    }
}
