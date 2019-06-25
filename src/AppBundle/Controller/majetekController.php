<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\majetek;

/**
 * majetek controller.
 *
 * @Route("/majetek")
 */
class majetekController extends Controller
{
    /**
     * Lists all majetek entities.
     *
     * @Route("/", name="majetek")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:majetek')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($majeteks, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('majetek/index.html.twig', array(
            'majeteks' => $majeteks,
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
        $filterForm = $this->createForm('AppBundle\Form\majetekFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('majetekControllerFilter');
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
                $session->set('majetekControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('majetekControllerFilter')) {
                $filterData = $session->get('majetekControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\majetekFilterType', $filterData);
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
            return $me->generateUrl('majetek', $requestParams);
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
     * Displays a form to create a new majetek entity.
     *
     * @Route("/new", name="majetek_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $majetek = new majetek();
        $form   = $this->createForm('AppBundle\Form\majetekType', $majetek);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($majetek);
            $em->flush();
            
            $editLink = $this->generateUrl('majetek_edit', array('id' => $majetek->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New majetek was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'majetek' : 'majetek_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('majetek/new.html.twig', array(
            'majetek' => $majetek,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a majetek entity.
     *
     * @Route("/{id}", name="majetek_show")
     * @Method("GET")
     */
    public function showAction(majetek $majetek)
    {
        $deleteForm = $this->createDeleteForm($majetek);
        return $this->render('majetek/show.html.twig', array(
            'majetek' => $majetek,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing majetek entity.
     *
     * @Route("/{id}/edit", name="majetek_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, majetek $majetek)
    {
        $deleteForm = $this->createDeleteForm($majetek);
        $editForm = $this->createForm('AppBundle\Form\majetekType', $majetek);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($majetek);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('majetek_edit', array('id' => $majetek->getId()));
        }
        return $this->render('majetek/edit.html.twig', array(
            'majetek' => $majetek,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a majetek entity.
     *
     * @Route("/{id}", name="majetek_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, majetek $majetek)
    {
    
        $form = $this->createDeleteForm($majetek);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($majetek);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The majetek was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majetek');
        }
        
        return $this->redirectToRoute('majetek');
    }
    
    /**
     * Creates a form to delete a majetek entity.
     *
     * @param majetek $majetek The majetek entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(majetek $majetek)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('majetek_delete', array('id' => $majetek->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete majetek by id
     *
     * @Route("/delete/{id}", name="majetek_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(majetek $majetek){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($majetek);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The majetek was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majetek');
        }

        return $this->redirect($this->generateUrl('majetek'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="majetek_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:majetek');

                foreach ($ids as $id) {
                    $majetek = $repository->find($id);
                    $em->remove($majetek);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'majeteks was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the majeteks ');
            }
        }

        return $this->redirect($this->generateUrl('majetek'));
    }
    

}
