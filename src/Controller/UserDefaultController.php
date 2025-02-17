<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/users/default')]
final class UserDefaultController extends AbstractController
{
    #[Route(name: 'app_user_default_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_default/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_user_default_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carYear = $request->request->get('car_year');
            $carMake = $request->request->get('car_make');
            $carModel = $request->request->get('car_model');
            $chipFile = $form->get('file')->getData();
            dump($carYear);
            if ($chipFile) {
                $originalFilename = pathinfo($chipFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFilename);
    
                $customFileName = sprintf(
                    '%s-%s-%s-%s-%s',
                    $safeFileName,
                    $carYear,
                    $carMake,
                    $carModel,
                    uniqid()
                );
    
                $newFilename = $customFileName . '.' . $chipFile->guessExtension();
                $chipFile->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );
    
                $user->setFile($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_default_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_default/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_user_default_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_default/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_user_default_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $carYear = $request->request->get('car_year');
            $carMake = $request->request->get('car_make');
            $carModel = $request->request->get('car_model');
            $chipFile = $form->get('file')->getData();
            dump($carYear);
            if ($chipFile) {
                $originalFilename = pathinfo($chipFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFilename);
    
                $customFileName = sprintf(
                    '%s-%s-%s-%s-%s',
                    $safeFileName,
                    $carYear,
                    $carMake,
                    $carModel,
                    uniqid()
                );
    
                $newFilename = $customFileName . '.' . $chipFile->guessExtension();
    
                $chipFile->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );

            $user->setFile($newFilename);
        }
    
            $entityManager->flush();

            return $this->redirectToRoute('app_user_default_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_user_default_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_default_index', [], Response::HTTP_SEE_OTHER);
    }
}
