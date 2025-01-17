<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'user_projects')]
    public function projects(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $projects = $projectRepository->findBy(['user' => $user]);

        return $this->render('user/projects.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/projects/create', name: 'user_project_create')]
    public function createProject(Request $request, EntityManagerInterface $em): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('screenshot')->getData();

            if ($logoFile) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $logoFile->guessExtension();

                // Déplacer le fichier uploadé vers le répertoire défini
                $logoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/screen_project',
                    $newFilename
                );

                // Mettre à jour le champ `logo` dans l'entité
                $project->setScreenshot('/uploads/screen_project/'.$newFilename);
            }
            $project->setUser($this->getUser());
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('user_projects');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/projects/{id}/edit', name: 'user_project_edit')]
    public function editProject(Project $project, Request $request, EntityManagerInterface $em): Response {
        {
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $logoFile = $form->get('screenshot')->getData();

                if ($logoFile) {
                    // Générer un nom unique pour le fichier
                    $newFilename = uniqid() . '.' . $logoFile->guessExtension();

                    // Déplacer le fichier uploadé vers le répertoire défini
                    $logoFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/screen_project',
                        $newFilename
                    );

                    // Mettre à jour le champ `logo` dans l'entité
                    $project->setScreenshot('/uploads/screen_project/'.$newFilename);
                }

                $em->persist($project);
                $em->flush();

                return $this->redirectToRoute('user_projects');
            }

            return $this->render('project/edit.html.twig', [
                'form' => $form->createView(),
                'project' => $project
            ]);
        }
    }


    #[Route('/projects/{id}/delete', name: 'user_project_delete', methods: ['POST'])]
    public function deleteProject(Project $project, EntityManagerInterface $em): Response
    {
        if ($project->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($project);
        $em->flush();

        return $this->redirectToRoute('user_projects');
    }
}
