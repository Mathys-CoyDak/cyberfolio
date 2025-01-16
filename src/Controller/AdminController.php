<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Technology;
use App\Entity\User;
use App\Form\ProjectType;
use App\Form\TechnologyType;
use App\Form\UserType;
use App\Repository\ProjectRepository;
use App\Repository\TechnologyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/projects', name: 'admin_projects')]
    public function projects(EntityManagerInterface $entityManager): Response
    {
        // Récupération des projets depuis la base de données
        $projects = $entityManager->getRepository(Project::class)->findAll();

        // Renvoi des projets à la vue
        return $this->render('admin/projects.html.twig', [
            'projects' => $projects,
        ]);
    }
    #[Route('/admin/users', name: 'admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        // Récupération des utilisateurs
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/technologies', name: 'admin_technologies')]
    public function technologies(EntityManagerInterface $entityManager): Response
    {
        // Récupération des technologies
        $technologies = $entityManager->getRepository(Technology::class)->findAll();

        return $this->render('admin/technologies.html.twig', [
            'technologies' => $technologies,
        ]);
    }


    #[Route('/admin/project/create', name: 'admin_project_create')]
    public function createProject(Request $request, EntityManagerInterface $em): Response
    {
        $project = new Project();

        // Création du formulaire de création de projet
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUser($this->getUser());
            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Le projet a été ajouté avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/project/edit/{id}', name: 'admin_project_edit')]
    public function editProject(int $id, Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em): Response
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        // Création du formulaire de modification de projet
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le projet a été modifié avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('/project/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }


    #[Route('/admin/project/delete/{id}', name: 'admin_project_delete')]
    public function deleteProject(int $id, ProjectRepository $projectRepository, EntityManagerInterface $em): Response
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $em->remove($project);
        $em->flush();

        $this->addFlash('success', 'Le projet a été supprimé avec succès !');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/user/create', name: 'admin_user_create')]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Création du formulaire de création d'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe avec UserPasswordHasherInterface
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été créé avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit')]
    public function editUser(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Création du formulaire de modification d'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été changé, le réencoder
            if ($user->getPassword()) {
                $password = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/admin/user/delete/{id}', name: 'admin_user_delete')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
        return $this->redirectToRoute('admin_dashboard');
    }
    #[Route('/admin/technology/create', name: 'admin_technology_create')]
    public function createTechnology(Request $request, EntityManagerInterface $em): Response
    {
        $technology = new Technology();

        // Création du formulaire de création de technologie
        $form = $this->createForm(TechnologyType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($technology);
            $em->flush();

            $this->addFlash('success', 'La technologie a été ajoutée avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('/technology/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/technology/edit/{id}', name: 'admin_technology_edit')]
    public function editTechnology(int $id, Request $request, TechnologyRepository $technologyRepository, EntityManagerInterface $em): Response
    {
        $technology = $technologyRepository->find($id);
        if (!$technology) {
            throw $this->createNotFoundException('Technologie non trouvée');
        }

        // Création du formulaire de modification de technologie
        $form = $this->createForm(TechnologyType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La technologie a été modifiée avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('/technology/edit.html.twig', [
            'form' => $form->createView(),
            'technology' => $technology,
        ]);
    }


    #[Route('/admin/technology/delete/{id}', name: 'admin_technology_delete')]
    public function deleteTechnology(int $id, TechnologyRepository $technologyRepository, EntityManagerInterface $em): Response
    {
        $technology = $technologyRepository->find($id);
        if (!$technology) {
            throw $this->createNotFoundException('Technologie non trouvée');
        }

        $em->remove($technology);
        $em->flush();

        $this->addFlash('success', 'La technologie a été supprimée avec succès !');
        return $this->redirectToRoute('admin_dashboard');
    }


}
