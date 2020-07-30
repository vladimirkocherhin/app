<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WordProgress;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Wordprogress controller.
 *
 * @Route("wordprogress")
 */
class WordProgressController extends Controller
{
    /**
     * Lists all wordProgress entities.
     *
     * @Route("/", name="wordprogress_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $wordProgresses = $em->getRepository('AppBundle:WordProgress')->findAll();

        return $this->render('wordprogress/index.html.twig', array(
            'wordProgresses' => $wordProgresses,
        ));
    }

    /**
     * Creates a new wordProgress entity.
     *
     * @Route("/new", name="wordprogress_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $wordProgress = new Wordprogress();
        $form = $this->createForm('AppBundle\Form\WordProgressType', $wordProgress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wordProgress);
            $em->flush();

            return $this->redirectToRoute('wordprogress_show', array('id' => $wordProgress->getId()));
        }

        return $this->render('wordprogress/new.html.twig', array(
            'wordProgress' => $wordProgress,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a wordProgress entity.
     *
     * @Route("/{id}", name="wordprogress_show")
     * @Method("GET")
     */
    public function showAction(WordProgress $wordProgress)
    {
        $deleteForm = $this->createDeleteForm($wordProgress);

        return $this->render('wordprogress/show.html.twig', array(
            'wordProgress' => $wordProgress,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing wordProgress entity.
     *
     * @Route("/{id}/edit", name="wordprogress_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, WordProgress $wordProgress)
    {
        $deleteForm = $this->createDeleteForm($wordProgress);
        $editForm = $this->createForm('AppBundle\Form\WordProgressType', $wordProgress);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('wordprogress_edit', array('id' => $wordProgress->getId()));
        }

        return $this->render('wordprogress/edit.html.twig', array(
            'wordProgress' => $wordProgress,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a wordProgress entity.
     *
     * @Route("/{id}", name="wordprogress_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, WordProgress $wordProgress)
    {
        $form = $this->createDeleteForm($wordProgress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wordProgress);
            $em->flush();
        }

        return $this->redirectToRoute('wordprogress_index');
    }

    /**
     * Creates a form to delete a wordProgress entity.
     *
     * @param WordProgress $wordProgress The wordProgress entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WordProgress $wordProgress)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wordprogress_delete', array('id' => $wordProgress->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
