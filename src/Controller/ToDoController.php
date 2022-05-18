<?php

namespace App\Controller;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController {
    #[Route('/todos', name: 'app_to_do')]
    public function index(EntityManagerInterface $em): Response {
        $todos = $em->getRepository(Todo::class)->findBy([], ['id'=>'ASC']);
        return $this->render('todo/index.html.twig', ['todos'=>$todos]);
    }
    
    #[Route('/create', name: 'create_todo', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): Response {
        $title = trim($request->get('title'));

        if (!empty($title)) {
            $entityManager = $doctrine->getManager();
            $todo = new Todo();
            $todo->setTitle($title);
            $entityManager->persist($todo);
            $entityManager->flush();
            // return $this->redirectToRoute('app_to_do');
            return $this->redirectToRoute('app_to_do');
        } else {
            return new Response('Please complete the form');
        }
    }

    #[Route('/update/{id}', name: 'update_todo')]
    public function update($id, ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();
        $todo = $entityManager->getRepository(Todo::class)->find($id);
        $todo->setStatus(!$todo->getStatus());
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do');
    }

    #[Route('/delete/{id}', name: 'delete_todo')]
    public function delete($id, ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();
        $todo = $entityManager->getRepository(Todo::class)->find($id);

        $entityManager->remove($todo);
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do');
    }
}
