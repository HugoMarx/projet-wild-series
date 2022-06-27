<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'new')]
    public function new(Request $request, CategorieRepository $categorieRepository)
    {
        $category = new Categorie;

        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
       
        if($form->isSubmitted()){
            $categorieRepository->add($category, true);      
        }

        // Render the form (best practice)
        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(CategorieRepository $categorieRepository, ProgramRepository $programRepository): Response
    {
        $categories = $categorieRepository->findAll();
        $itemByCategory = [];

        foreach ($categories as $item) {
            $itemByCategory[$item->getName()] = count($item->getPrograms());
        }

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'itemByCategory' => $itemByCategory
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
