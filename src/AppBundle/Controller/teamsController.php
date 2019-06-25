<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\teams;

/**
 * teams controller.
 *
 * @Route("/tymy")
 */
class teamsController extends Controller
{
    /**
     * Lists all teams entities.
     *
     * @Route("/", name="tymy")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:teams')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($teams, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('teams/index.html.twig', array(
            'teams' => $teams,
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
        $filterForm = $this->createForm('AppBundle\Form\teamsFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('teamsControllerFilter');
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
                $session->set('teamsControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('teamsControllerFilter')) {
                $filterData = $session->get('teamsControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\teamsFilterType', $filterData);
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
            return $me->generateUrl('tymy', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'předchozí',
            'next_message' => 'další',
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
        return "Zobrazeno $startRecord - $endRecord z $totalOfRecords záznamů";
    }
    
    

    /**
     * Displays a form to create a new teams entity.
     *
     * @Route("/new", name="tymy_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $team = new teams();
        $form   = $this->createForm('AppBundle\Form\teamsType', $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            
            $editLink = $this->generateUrl('tymy_edit', array('id' => $team->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Nový tým úspěšně vytvořen.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'tymy' : 'tymy_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('teams/new.html.twig', array(
            'team' => $team,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a teams entity.
     *
     * @Route("/{id}", name="tymy_show")
     * @Method("GET")
     */
    public function showAction(teams $team)
    {
        $deleteForm = $this->createDeleteForm($team);
        return $this->render('teams/show.html.twig', array(
            'team' => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing teams entity.
     *
     * @Route("/{id}/edit", name="tymy_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, teams $team)
    {
        $deleteForm = $this->createDeleteForm($team);
        $editForm = $this->createForm('AppBundle\Form\teamsType', $team);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Úspěšně upraveno!');
            return $this->redirectToRoute('tymy_edit', array('id' => $team->getId()));
        }
        return $this->render('teams/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a teams entity.
     *
     * @Route("/{id}", name="tymy_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, teams $team)
    {
    
        $form = $this->createDeleteForm($team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Tým úspěšně odstaněn');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problém s odstraněním týmu');
        }
        
        return $this->redirectToRoute('tymy');
    }
    
    /**
     * Creates a form to delete a teams entity.
     *
     * @param teams $team The teams entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(teams $team)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tymy_delete', array('id' => $team->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete teams by id
     *
     * @Route("/delete/{id}", name="tymy_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(teams $team){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($team);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Tým úspěšně odstaněn');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problém s odstraněním týmu');
        }

        return $this->redirect($this->generateUrl('tymy'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="tymy_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:teams');

                foreach ($ids as $id) {
                    $team = $repository->find($id);
                    $em->remove($team);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Týmy úspěšně odstaněny!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problém s odstraněním týmů ');
            }
        }

        return $this->redirect($this->generateUrl('tymy'));
    }
  
       /**
     * @Route("/tymy/pdf", name="pdf_tymy")
     */


    public function pdfTeamsAction(Request $request)
    {
       $pdf = $this->container->get("white_october.tcpdf")->create(
            'Portrait',
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'ISO-8859-1',
            false
        );
        $pdf->SetFont('dejavusans', '', 13, '', true);
        $pdf->SetTitle('tymy.pdf');
        $pdf->AddPage();

       
        $html = '<h1 style="text-align:center;">Týmy</h1>';
        $html .= '<table>
              <thead>
               <tr>
                  <th>ID</th>
                 <th>NÁZEV</th>
                 <th>LIGA</th>
                 <th>MĚSTO</th>
                 <th>STÁT</th>
               </tr>
               </thead>';
        $html .='<tbody>';
          
         $html .='<tr>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
               </tr>';
         $html .= '</tbody>';                    
        $html .= '</table>';
       
        
         $pdf->writeHTMLCell(
            $w = 0,
            $h = 0,
            $x = '',
            $y = '',
            $html,
            $border = 1,
            $ln = 4,
            $fill = 0,
            $reseth = true,
            $align = '',
            $autopadding = true
        );
        $pdf->Output("tymy.pdf", 'I');
    
}    

}
