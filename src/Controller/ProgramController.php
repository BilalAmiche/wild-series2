<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProgramType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


/**
* @Route("/program", name="program_")
*/
class ProgramController extends AbstractController
{
   /**
    * @Route("/", name="index")
    * @return Response
    */
    public function index(ProgramRepository $programRepository): Response
    {
        
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
         ]);
        }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
    
            $entityManager->persist($program);
            $entityManager->flush();
            
            return $this->redirectToRoute('program_index');
    
        }

        return $this->render('program/new.html.twig', ["form" => $form->createView()]);

    }


           /**
    * @Route("/{id}", requirements={"page"="\d+"}, methods={"GET"}, name="show")
    * @return Response
    */
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', ['program' => $program]);
    }

     /**
     * @Route("/{programId}/season/{seasonId}", requirements={"id"="\d+"}, methods={"GET"}, name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     */
    public function showSeason(Program $program, Season $season, Episode $episodes, EpisodeRepository $episodeRepository): Response
    {
        $episodes = $episodeRepository->findBySeason($season);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }
    /**
     * @Route("/{programId}/season/{seasonId}/episode/{episodeId}", methods={"GET"}, name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }

}