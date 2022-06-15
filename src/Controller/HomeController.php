<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, TodoListRepository $todoListRepository): Response
    {
        // déclare une nouvelle instance de mon entité todolist
        $todoList = new TodoList();
        $showLists = $todoListRepository->findAll();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($todoList);
            $entityManager->flush();
            // redirection sur la liste créée

        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'lists' => $showLists
        ]);
    }

     #[Route('/home/list{id}', name: 'app_home_list')]
    public function list(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepository, $id, TodoListRepository $toDoRepository): Response
    {
        $taskList = new Task();
        $list = $toDoRepository->findOneBy([ 'id' => $id ]);
        //$showTasks = $taskRepository->findAll();
        $showTasks = $taskRepository->findBy([ 'list' => $id ]);
        $taskForm = $this->createForm(TaskType::class, $taskList);
        $taskForm->handleRequest($request);

        if ($taskForm->isSubmitted() && $taskForm->isValid()){
            $taskList->setList($list);
            $entityManager->persist($taskList);
            $entityManager->flush();
        }

        return $this->render(
            'home/list.html.twig', [
            'taskForm' => $taskForm->createView(),
            'taskLists' => $showTasks
                   
        ]);
    }

    // #[Route('/home/listDelete{id}', name: 'app_home_delete')]
    // public function delete(EntityManagerInterface $entityManager, $id): Response
    // {
    //     $entityManager->remove($id);
    //     $entityManager->flush();

    //     return $this->redirectToRoute('app_home_list');     
    // }
}
