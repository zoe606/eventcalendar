<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="category_list")
     */
    public function indexAction(Request $request)
    {
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        // render template
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/create", name="category_create")
     */
    public function createAction(Request $request)
    {
        $category = new Category;

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create Category', 'attr' => array('class' => 'btn btn-primary')))
            ->getForm();

            // handle request
            $form->handleRequest($request);

            // check submit
            if ($form->isSubmitted() && $form->isValid()) {
                $name = $form['name']->getData();
                // get current date and time
                $now = new \DateTime('now');

                $category->setName($name);
                $category->setCreateDate($now);
                 print_r($category);
                die();

                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Category Saved'
                );

                return $this->redirectToRoute('category_list');
            }

            // render template
        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function editAction($id, Request $request)
    {
        $category = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id' . $id
            );
        }
        $category->setName($category->getName());        

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create Category', 'attr' => array('class' => 'btn btn-primary')))
            ->getForm();

            // handle request
            $form->handleRequest($request);

            // check submit
            if ($form->isSubmitted() && $form->isValid()) {
                $name = $form['name']->getData();

 
                //  print_r($category);
                // die();

                $em = $this->getDoctrine()->getManager();
                $category = $em->getRepository('AppBundle:Category')->find($id);

                $category->setName($name);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Category Updated'
                );

                return $this->redirectToRoute('category_list');
            }

            // render template
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No Category found with the id'. $id
            );
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'notice',
            'Category Deleted'
        );

        return $this->redirectToRoute('category_list');
    }

}
