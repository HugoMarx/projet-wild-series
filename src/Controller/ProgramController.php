<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Comment;
use App\Form\ProgramType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, Session $session,): Response
    {
        dump($session->getFlashBag());
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
            $this->addFlash('success', 'Nouvelle série enregistrée avec succès !');
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

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);
            $this->addFlash('danger', 'Série supprimée avec succès');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{slug}/seasons/{season}', name: 'season_show')]
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


    /** @var \App\Entity\User $user */
    #[Route('/{slug}/seasons/{season}/episode/{episode_slug}', name: 'episode_show')]
    #[Entity('episode', expr: 'repository.findBySlug(episode_slug)')]
    #[ParamConverter('episode', options: ['mapping' => ['episode_slug' => 'slug']])]
    public function showEpisode(
        Program $program,
        Season $season,
        Episode $episode,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $manager,
    ) {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisodeId($episode);
            $commentRepository->add($commentForm->getData());
            $manager->persist($comment);
            $manager->flush();
        }

        $comments = $commentRepository->findByEpisodeId($episode, ['id' => 'DESC']);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $commentForm->createView(),
            'comments' => $comments

        ]);
    }

    #[Route('/{id}/watchlist', name: 'watchlist_add')]
    public function addToWatchlist(Program $program, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        if($user->isInWatchlist($program)){
            $user->removeFromWatchlist($program);
        } else {
            $user->addToWatchlist($program);
        }
        $manager->flush();

        return $this->json(
            ['isInWatchlist' => $user->isInWatchlist($program)]
        );
    }
}
