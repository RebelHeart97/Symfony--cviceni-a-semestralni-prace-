<?php

namespace AppBundle\Controller;

use AppBundle\Entity\sklad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Sklad controller.
 *
 * @Route("cv8")
 */
class skladController extends Controller
{
    /**
     * Lists all sklad entities.
     *
     * @Route("/", name="cv8_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sklads = $em->getRepository('AppBundle:sklad')->findAll();

        return $this->render('sklad/index.html.twig', array(
            'sklads' => $sklads,
        ));
    }

    /**
     * Creates a new sklad entity.
     *
     * @Route("/new", name="cv8_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sklad = new Sklad();
        $form = $this->createForm('AppBundle\Form\skladType', $sklad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sklad);
            $em->flush();

            return $this->redirectToRoute('cv8_show', array('id' => $sklad->getId()));
        }

        return $this->render('sklad/new.html.twig', array(
            'sklad' => $sklad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sklad entity.
     *
     * @Route("/{id}", name="cv8_show")
     * @Method("GET")
     */
    public function showAction(sklad $sklad)
    {
        $deleteForm = $this->createDeleteForm($sklad);

        return $this->render('sklad/show.html.twig', array(
            'sklad' => $sklad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sklad entity.
     *
     * @Route("/{id}/edit", name="cv8_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, sklad $sklad)
    {
        $deleteForm = $this->createDeleteForm($sklad);
        $editForm = $this->createForm('AppBundle\Form\skladType', $sklad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cv8_edit', array('id' => $sklad->getId()));
        }

        return $this->render('sklad/edit.html.twig', array(
            'sklad' => $sklad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sklad entity.
     *
     * @Route("/{id}", name="cv8_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, sklad $sklad)
    {
        $form = $this->createDeleteForm($sklad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sklad);
            $em->flush();
        }

        return $this->redirectToRoute('cv8_index');
    }

    /**
     * Creates a form to delete a sklad entity.
     *
     * @param sklad $sklad The sklad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(sklad $sklad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cv8_delete', array('id' => $sklad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
