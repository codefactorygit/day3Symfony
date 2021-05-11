<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;

use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TodoController extends AbstractController
{
    # private $em = $this->getDoctrine()->getManager();

    
    /**
     * @Route("/", name="todo")
     */
    public function index(): Response
    {
        $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();
        # dd($todos);

        return $this->render('todo/index.html.twig', [
            "todos" => $todos
        ]);
    }

    /**
     * @Route("/create", name="todo_create")
     */
    public function createAction(Request $request): Response
    {
        $todo = new Todo;
        # dd($todo);
        
        $form = $this->createFormBuilder($todo)->add('name', TextType::class, array("attr"=>array("class"=>"form-control", "style"=>"margin-bottom:15px", "id"=>"demo")))
        ->add('category', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))

        ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))

        ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High' ),'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))

        ->add('due_date', DateTimeType::class, array('attr' => array('style'=>'margin-bottom:15px')))

        ->add('save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))

        ->getForm();

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            # dd($request);
            $name = $form["name"]->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);

            $todo->setPriority($priority);

            $todo->setDueDate($due_date);

            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();
            $this->addFlash(
                "notice",
                "Todo Added to your database"
            );
            return $this->redirectToRoute("todo");
            # dd($now);
        }
        # <input name="name" type="text" class="form-control" style="margin-bottom:15px">
        return $this->render('todo/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request): Response
    {
        $todo = $this->getDoctrine()->getRepository('App:Todo')->find($id);
        # select * from todo where id = $_GET["id"]
        
        $now = new\DateTime('now');
    
         $form = $this->createFormBuilder($todo)->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('category', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low', 'Normal'=>'Normal', 'High'=>'High'),'attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
             ->add('due_date', DateTimeType::class, array('attr' => array('style'=>'margin-bottom:15px')))
             ->add('save', SubmitType::class, array('label'=> 'Update Todo', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-botton:15px')))
             ->getForm();
        $form->handleRequest($request);
 
 
        if($form->isSubmitted() && $form->isValid()){
            //fetching data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $now = new\DateTime('now');
            $em = $this->getDoctrine()->getManager();
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);
            #$em->persist($todo);
            $em->flush();
            $this->addFlash(
                    'notice',
                    'Todo Updated'
                    );
            return $this->redirectToRoute('todo');
        }
        return $this->render('todo/edit.html.twig', array('todo' => $todo, 'form' => $form->createView())); 
    }

    /**
     * @Route("/details/{id}", name="todo_details")
     */
    public function detailsAction($id): Response
    {
        return $this->render('todo/details.html.twig', [
            
        ]);
    }
}
