<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/category", name="category_")
*/
class CategoryController extends AbstractController
{
   /**
    * @Route("/", name="index")
    * @return Response
    */
    public function index(CategoryRepository $categoryRepository): Response
    {
        
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
         ]);
        }

           /**
    * @Route("/{name}", requirements={"page"="\d+"}, methods={"GET"}, name="show")
    * @return Response
    */
    public function show(CategoryRepository $categoryRepository, ProgramRepository $programRepository, string $name): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $name]);

    if (!$category) {
        throw $this->createNotFoundException(
            'No category named : '.$name.' found in program\'s table.'
        );
    }

    $programs = $programRepository->findByCategory($category, ['id' => 'desc'], 3);

    return $this->render('category/show.html.twig', [
        'category' => $category,
        'programs' => $programs,
    ]);
    }
}