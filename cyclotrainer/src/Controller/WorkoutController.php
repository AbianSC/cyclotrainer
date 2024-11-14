<?php

namespace App\Controller;

use App\Entity\Workout;
use App\Form\WorkoutType;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/workout')]
class WorkoutController extends AbstractController
{
    #[Route('/', name: 'app_workout_index', methods: ['GET'])]
    public function index(WorkoutRepository $workoutRepository): Response
    {
        return $this->render('workout/index.html.twig', [
            'workouts' => $workoutRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'create_workout', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workout = new Workout();
        $form = $this->createForm(WorkoutType::class, $workout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workout->setUser($this->getUser());
            foreach($workout->getWorkoutExercises() as $exercise) {
                $exercise->setWorkout($workout);
                $entityManager->persist($exercise);
            }
            $entityManager->persist($workout);
            $entityManager->flush();

            return $this->redirectToRoute('workouts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout/new.html.twig', [
            'workoutForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workout_show', methods: ['GET'])]
    public function show(Workout $workout): Response
    {
        return $this->render('workout/show.html.twig', [
            'workout' => $workout,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workout_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workout $workout, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkoutType::class, $workout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_workout_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout/edit.html.twig', [
            'workout' => $workout,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_delete', methods: ['POST'])]
    public function delete(Request $request, Workout $workout, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workout->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workout);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workout_index', [], Response::HTTP_SEE_OTHER);
    }
}


