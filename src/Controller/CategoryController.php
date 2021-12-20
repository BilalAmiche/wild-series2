<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;

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
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request) : Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $entityManager = $this->getDoctrine()->getManager();    
            $entityManager->persist($category);
            $entityManager->flush();
            
            return $this->redirectToRoute('category_index');
    
        }

        return $this->render('category/new.html.twig', ["form" => $form->createView()]);

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