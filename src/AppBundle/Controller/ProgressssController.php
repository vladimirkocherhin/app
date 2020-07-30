<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Progress;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Progress controller.
 *
 * @Route("progress")
 */
class ProgresssController extends Controller
{
    /**
     * Lists all progress entities.
     *
     * @Route("/", name="progress_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $progresses = $em->getRepository('AppBundle:Progress')->findAll();

        return $this->render('progress/index.html.twig', array(
            'progresses' => $progresses,
        ));
    }

    /**
     * Creates a new progress entity.
     *
     * @Route("/new", name="progress_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $progress = new Progress();
        $form = $this->createForm('AppBundle\Form\ProgressType', $progress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($progress);
            $em->flush();

            return $this->redirectToRoute('progress_show', array('id' => $progress->getId()));
        }

        return $this->render('progress/new.html.twig', array(
            'progress' => $progress,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a progress entity.
     *
     * @Route("/{id}", name="progress_show")
     * @Method("GET")
     */
    public function showAction(Progress $progress)
    {
        $deleteForm = $this->createDeleteForm($progress);

        return $this->render('progress/show.html.twig', array(
            'progress' => $progress,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing progress entity.
     *
     * @Route("/{id}/edit", name="progress_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Progress $progress)
    {
        $deleteForm = $this->createDeleteForm($progress);
        $editForm = $this->createForm('AppBundle\Form\ProgressType', $progress);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('progress_edit', array('id' => $progress->getId()));
        }

        return $this->render('progress/edit.html.twig', array(
            'progress' => $progress,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a progress entity.
     *
     * @Route("/{id}", name="progress_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Progress $progress)
    {
        $form = $this->createDeleteForm($progress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($progress);
            $em->flush();
        }

        return $this->redirectToRoute('progress_index');
    }

    /**
     * Creates a form to delete a progress entity.
     *
     * @param Progress $progress The progress entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Progress $progress)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('progress_delete', array('id' => $progress->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    //////////////////////////////////

    /**
     * @Route("/asd/")
     * @param string $slug
     * @return Response
     * @throws InvalidArgumentException
     */
    public function train(string $slug)
    {

        /** @var $repository Repository */
        $repository = $this->get( 'ezpublish.api.repository' );
        $searchService = $repository->getSearchService();

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ParentLocationId( 56 ),
            new Criterion\ContentTypeIdentifier(['word']),
        ]);

        $em = $this->getDoctrine()->getManager();
        $result = $searchService->findContent($query);



        //dump($slug, $result); die;
        $username = $this->getUser()->getUsername();
        dump($username);die;
        //check if he has progress
        //if not, create it
        //if yes - do something else

        $progress = new Progress();
        $progress->setUsername('123');

        $em->persist($progress);
        $em->flush();

        return new Response('space rocks... include comets, asteroids & meteoroids');
    }




}
