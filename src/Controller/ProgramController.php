<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series',
            'programs' => $programs
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, Slugify $slugify, MailerInterface $mailer)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $programRepository->add($program, true);

            $email = (new TemplatedEmail())
                ->from('wilder@wildcodeschool.com')
                ->to(new Address('your_email@example.com', 'Hugo'))
                ->subject('Une nouvelle série vient d\'être publiée !')
                // path of the Twig template to render
                ->htmlTemplate('program/newProgramEmail.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'program' => $program,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('program/new.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/show/{slug}', name: 'show')]
    public function show(Program $program, SeasonRepository $seasonRepository): Response
    {

        $seasons = $seasonRepository->findByProgram($program);


        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons

        ]);
    }

    #[Route('/{program}/seasons/{season}', name: 'season_show')]
    public function showSeason(
        Program $program,
        Season $season,
        EpisodeRepository $episodeRepository

    ): Response {
        $episodes = $episodeRepository->findBySeason($season, ['number' => 'ASC']);
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }

    #[Route('/{program}/seasons/{season}/episode/{episode}', name: 'episode_show')]
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}
