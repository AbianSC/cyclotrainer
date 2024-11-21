<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\History;
use App\Entity\Workout;
use App\Entity\Exercise;
use App\Form\WorkoutType;
use App\Repository\HistoryRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

            return $this->redirectToRoute('dashboard_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout/new.html.twig', [
            'workoutForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workout_show', methods: ['GET', 'POST'])]
    public function show(Workout $workout, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // Verificar si el entrenamiento ha comenzado (verificamos la sesión)
        $startTime = $request->getSession()->get('start_time_' . $workout->getId());
        $isStarted = $startTime !== null;

        // Obtener el valor de la acción desde el formulario
        $action = $request->request->get('action'); // Obtener la acción

        // Acciones diferentes basadas en el valor de "action"
        if ($action === 'start') {
            // Si no está iniciado, iniciar el entrenamiento
            $startTime = new \DateTime();
            $request->getSession()->set('start_time_' . $workout->getId(), $startTime->format('Y-m-d H:i:s'));
            $this->addFlash('success', 'Entrenamiento iniciado');
            return $this->redirectToRoute('app_workout_show', ['id' => $workout->getId()]);
        }

        if ($action === 'end' && $isStarted) {
            // Si el entrenamiento ya está en progreso, finalizarlo
            $endTime = new \DateTime();
            $startTime = new \DateTime($startTime);

            // Calcular la duración como un DateInterval
            $duration = $startTime->diff($endTime);

            // Convertir a segundos
            $durationInSeconds = $duration->h * 3600 + $duration->i * 60 + $duration->s;

            // Formatear la duración a hh:mm:ss
            $formattedDuration = $this->formatDuration($durationInSeconds);

            // Guardar el historial del entrenamiento
            $trainingHistory = new History();
            $trainingHistory->setWorkout($workout);
            $trainingHistory->setWorkoutName($workout->getName());   // Crear el nombre del historial (el mismo que el de la plantilla)
            $trainingHistory->setDuration($formattedDuration);
            $trainingHistory->setDateWorkout($endTime);  // La fecha de finalización
            $trainingHistory->setUser($user);

            // Guardar el historial en la base de datos
            $entityManager->persist($trainingHistory);
            $entityManager->flush();

            // Limpiar la sesión para el próximo entrenamiento
            $request->getSession()->remove('start_time_' . $workout->getId());

            $this->addFlash('success', 'Entrenamiento finalizado. Duración: ' . $formattedDuration);
            return $this->redirectToRoute('history');
        }

        if ($action === 'delete') {
            // Eliminar el entrenamiento
            $entityManager->remove($workout);
            $entityManager->flush();

            $this->addFlash('success', 'Entrenamiento eliminado');
            return $this->redirectToRoute('dashboard_user');
        }

        // Renderizar la vista con los datos del entrenamiento
        return $this->render('workout/show.html.twig', [
            'workout' => $workout,
            'user' => $user,
            'isStarted' => $isStarted,
            'startTime' => $startTime,
        ]);
    }

    private function formatDuration(int $durationInSeconds): string
    {
        $hours = floor($durationInSeconds / 3600);
        $minutes = floor(($durationInSeconds % 3600) / 60);
        $seconds = $durationInSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Devuelve en formato hh:mm:ss
    }

    #[Route('/exercise/{id}', name: 'app_exercise_show', methods: ['GET'])]
    public function showExercise(Exercise $exercise, Request $request): Response
    {
        $referer = $request->headers->get('referer');

        return $this->render('workout/exercise.html.twig', [
            'exercise' => $exercise,
            'referer' => $referer,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_workout_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workout $workout, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->headers->get('referer');
        $form = $this->createForm(WorkoutType::class, $workout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workout->setUpdateAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_workout_show', ['id' => $workout->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout/edit.html.twig', [
            'workout' => $workout,
            'referer' => $referer,
            'workoutForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workout_delete', methods: ['POST'])]
    public function delete(Workout $workout, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Asegúrate de que la acción de eliminación está siendo realizada por una solicitud POST
        if ($this->isCsrfTokenValid('delete' . $workout->getId(), $request->request->get('_token'))) {

            // Desvincula los registros de History del Workout
            foreach ($workout->getHistory() as $history) {
                $history->setWorkout(null); // Esto desvincula el workout de la tabla history
            }

            // Persistir los cambios (solo para desvincular el workout de History)
            $entityManager->flush();

            // Elimina el workout (sin eliminar el historial)
            $entityManager->remove($workout);
            $entityManager->flush();

            $this->addFlash('success', 'Entrenamiento eliminado, pero el historial se mantiene.');
            return $this->redirectToRoute('dashboard_user');
        }

        // Si no es una solicitud válida, redirige al usuario
        return $this->redirectToRoute('dashboard_user');
    }

}


