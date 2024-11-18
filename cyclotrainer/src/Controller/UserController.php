<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\History;
use App\Entity\Workout;
use App\Entity\Exercise;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\HistoryRepository;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/dash', name: 'dashboard_user')]
    public function dashboard(EntityManagerInterface $entityManager, UserRepository $userRepository, Security $security)
    {   
        $user = $security->getUser();
        $myWorkouts = $entityManager->getRepository(Workout::class)->findBy(['user' => $user]);
        return $this->render('user/dashboard.html.twig', [
            'workouts' => $myWorkouts,
            'user' => $user
        ]);
    }

    #[Route('/dash/history', name: 'history')]
    public function history(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $histories = $entityManager->getRepository(History::class)->findBy(['user' => $user]);
        return $this->render('user/history.html.twig', [
            'histories' => $histories,
        ]);
    }

    #[Route('/dash/workouts', name: 'workouts')]
    public function workouts()
    {
        return $this->render('user/workouts.html.twig');
    }

    #[Route('/dash/exercises', name: 'exercises')]
    public function exercises(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAllNames();
        return $this->render('user/exercises.html.twig', [
            'exercises' => $exercises,
        ]);

    }

    #[Route('/dash/exercises/{id}', name: 'exercise_detail')]
    public function detail(Exercise $exercise): Response
    {
        return $this->render('user/detail.html.twig', [
            'exercise' => $exercise,
        ]);

    }
    
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
