<?php

namespace App\Controller;

use App\Entity\Technology;
use App\Form\TechnologyType;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TechnologyController extends AbstractController
{
    #[Route('/technologies', name: 'user_technologies')]
    public function technologies(TechnologyRepository $technologyRepository): Response
    {
        $technologies = $technologyRepository->findAll();

        return $this->render('user/technologies.html.twig', [
            'technologies' => $technologies,
        ]);
    }


    #[Route('/technologies/create', name: 'technology_create')]
    public function createTechnology(Request $request, EntityManagerInterface $em): Response
    {
        $technology = new Technology();
        $form = $this->createForm(TechnologyType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $logoFile->guessExtension();

                // Déplacer le fichier uploadé vers le répertoire défini
                $logoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/logos',
                    $newFilename
                );

                // Mettre à jour le champ `logo` dans l'entité
                $technology->setLogo('/uploads/logos/'.$newFilename);
            }

            $em->persist($technology);
            $em->flush();

            return $this->redirectToRoute('user_technologies');
        }

        return $this->render('technology/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/technologies/{id}/edit', name: 'technology_edit')]
    public function editTechnology(Technology $technology, Request $request, EntityManagerInterface $em): Response {
        $form = $this->createForm(TechnologyType::class, $technology);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload du logo
            $logoFile = $form->get('logo')->getData();
            if ($logoFile) {
                // Créer un nom unique pour l'image
                $newFilename = uniqid().'.'.$logoFile->guessExtension();

                try {
                    // Déplacez le fichier dans le répertoire où vous voulez le stocker
                    $logoFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/logos',
                        $newFilename
                    );
                    // Mettez à jour le chemin du logo dans l'entité
                    $technology->setLogo('/uploads/logos/'.$newFilename);
                } catch (FileException $e) {
                    // Si une erreur survient, affichez un message d'erreur
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            // Sauvegarde dans la base de données
            $em->flush();

            return $this->redirectToRoute('user_technologies');
        }

        return $this->render('technology/edit.html.twig', [
            'form' => $form->createView(),
            'technology' => $technology
        ]);
    }
    #[Route('/technologies/{id}/delete', name: 'technology_delete', methods: ['POST'])]
    public function deleteTechnology(Technology $technology, EntityManagerInterface $em): Response
    {
        $logoPath = $this->getParameter('kernel.project_dir').'/public/uploads/logos' . '/' . $technology->getLogo();

        // Vérifie si le fichier existe et le supprime
        if (file_exists($logoPath)) {
            unlink($logoPath);
        }

        $em->remove($technology);
        $em->flush();

        return $this->redirectToRoute('user_technologies');
    }

}
