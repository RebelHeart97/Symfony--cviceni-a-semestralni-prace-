<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\majitel;

/**
 * majitel controller.
 *
 * @Route("/majitel")
 */
class majitelController extends Controller
{
    /**
     * Lists all majitel entities.
     *
     * @Route("/", name="majitel")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:majitel')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($majitels, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('majitel/index.html.twig', array(
            'majitels' => $majitels,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\majitelFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('majitelControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('majitelControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('majitelControllerFilter')) {
                $filterData = $session->get('majitelControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\majitelFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('majitel', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    
    
    /*
     * Calculates the total of records string
     */
    protected function getTotalOfRecordsString($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Showing $startRecord - $endRecord of $totalOfRecords Records.";
    }
    
    

    /**
     * Displays a form to create a new majitel entity.
     *
     * @Route("/new", name="majitel_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $majitel = new majitel();
        $form   = $this->createForm('AppBundle\Form\majitelType', $majitel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($majitel);
            $em->flush();
            
            $editLink = $this->generateUrl('majitel_edit', array('id' => $majitel->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New majitel was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'majitel' : 'majitel_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('majitel/new.html.twig', array(
            'majitel' => $majitel,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a majitel entity.
     *
     * @Route("/{id}", name="majitel_show")
     * @Method("GET")
     */
    public function showAction(majitel $majitel)
    {
        $deleteForm = $this->createDeleteForm($majitel);
        return $this->render('majitel/show.html.twig', array(
            'majitel' => $majitel,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing majitel entity.
     *
     * @Route("/{id}/edit", name="majitel_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, majitel $majitel)
    {
        $deleteForm = $this->createDeleteForm($majitel);
        $editForm = $this->createForm('AppBundle\Form\majitelType', $majitel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($majitel);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('majitel_edit', array('id' => $majitel->getId()));
        }
        return $this->render('majitel/edit.html.twig', array(
            'majitel' => $majitel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a majitel entity.
     *
     * @Route("/{id}", name="majitel_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, majitel $majitel)
    {
    
        $form = $this->createDeleteForm($majitel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($majitel);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The majitel was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majitel');
        }
        
        return $this->redirectToRoute('majitel');
    }
    
    /**
     * Creates a form to delete a majitel entity.
     *
     * @param majitel $majitel The majitel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(majitel $majitel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('majitel_delete', array('id' => $majitel->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete majitel by id
     *
     * @Route("/delete/{id}", name="majitel_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(majitel $majitel){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($majitel);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The majitel was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majitel');
        }

        return $this->redirect($this->generateUrl('majitel'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="majitel_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:majitel');

                foreach ($ids as $id) {
                    $majitel = $repository->find($id);
                    $em->remove($majitel);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'majitels was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majitels ');
            }
        }

        return $this->redirect($this->generateUrl('majitel'));
    }
    

}
