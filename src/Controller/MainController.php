<?php

namespace App\Controller;

use App\Entity\Main;
use App\Form\MainType;
use App\Entity\Structure;
use App\Repository\MainRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/main')]
final class MainController extends AbstractController
{
    #[Route('/home',name: 'app_home', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function home(MainRepository $mainRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'mains' => $mainRepository->findAll(),
        ]);
    }

    #[Route(name: 'app_main_index', methods: ['GET'])]
    public function index(MainRepository $mainRepository, StructureRepository $structureRepository): Response
    {
        $mains = $mainRepository->findAll();
        $structures = $structureRepository->findAll();
        foreach ($mains as $main) {
            $backgroundImage = $main->getBackgroundImage();
            $main->isVideo = $this->isVideo($backgroundImage);
            
        }
        return $this->render('main/index.html.twig', [
            'mains' => $mains,
            'structures' => $structures,
        ]);
    }
    // Méthode qui permet de vérifier le type de background_image
    private function isVideo(string $filename): bool
    {
        $videoExtensions = ['mp4', 'webm', 'ogg','gif'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($extension, $videoExtensions);
    }
    #[Route('/new', name: 'app_main_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $main = new Main();
        $form = $this->createForm(MainType::class, $main);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($main);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/new.html.twig', [
            'main' => $main,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Main $main): Response
    {
        return $this->render('main/show.html.twig', [
            'main' => $main,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Main $main, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(MainType::class, $main);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoFile = $form->get('background_image')->getData();
            
            if($videoFile){
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFilename);
                $newFilename = $safeFileName.'-'. uniqid() . '.' . $videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $newFilename
                );
                $main->setBackgroundImage($newFilename);
            }
            $entityManager->persist($main);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/edit.html.twig', [
            'main' => $main,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Main $main, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$main->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($main);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_main_index', [], Response::HTTP_SEE_OTHER);
    }
}
