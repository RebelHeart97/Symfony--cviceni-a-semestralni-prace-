<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\players;

/**
 * players controller.
 *
 * @Route("/hraci")
 */
class playersController extends Controller
{
    /**
     * Lists all players entities.
     *
     * @Route("/", name="hraci")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:players')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($players, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('players/index.html.twig', array(
            'players' => $players,
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
        $filterForm = $this->createForm('AppBundle\Form\playersFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('playersControllerFilter');
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
                $session->set('playersControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('playersControllerFilter')) {
                $filterData = $session->get('playersControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\playersFilterType', $filterData);
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
            return $me->generateUrl('hraci', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'předchozí',
            'next_message' => 'následující',
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
        return "Zobrazeno $startRecord - $endRecord z $totalOfRecords záznamů.";
    }
    
    

    /**
     * Displays a form to create a new players entity.
     *
     * @Route("/new", name="hraci_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $player = new players();
        $form   = $this->createForm('AppBundle\Form\playersType', $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();
            
            $editLink = $this->generateUrl('hraci_edit', array('id' => $player->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Nový hráč byl úspěšně vytvořen.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'hraci' : 'hraci_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('players/new.html.twig', array(
            'player' => $player,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a players entity.
     *
     * @Route("/{id}", name="hraci_show")
     * @Method("GET")
     */
    public function showAction(players $player)
    {
        $deleteForm = $this->createDeleteForm($player);
        return $this->render('players/show.html.twig', array(
            'player' => $player,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing players entity.
     *
     * @Route("/{id}/edit", name="hraci_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, players $player)
    {
        $deleteForm = $this->createDeleteForm($player);
        $editForm = $this->createForm('AppBundle\Form\playersType', $player);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Úspěšně upraveno!');
            return $this->redirectToRoute('hraci_edit', array('id' => $player->getId()));
        }
        return $this->render('players/edit.html.twig', array(
            'player' => $player,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a players entity.
     *
     * @Route("/{id}", name="hraci_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, players $player)
    {
    
        $form = $this->createDeleteForm($player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Hráč byl úspěšně smazán');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problém s odstaněním hráče');
        }
        
        return $this->redirectToRoute('hraci');
    }
    
    /**
     * Creates a form to delete a players entity.
     *
     * @param players $player The players entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(players $player)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hraci_delete', array('id' => $player->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete players by id
     *
     * @Route("/delete/{id}", name="hraci_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(players $player){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($player);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Hráč byl úspěšně smazán');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problém s odstaněním hráče');
        }

        return $this->redirect($this->generateUrl('hraci'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="hraci_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:players');

                foreach ($ids as $id) {
                    $player = $repository->find($id);
                    $em->remove($player);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Hráči byli úspěšně smazáni!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problém s odstaněním hráčů ');
            }
        }

        return $this->redirect($this->generateUrl('hraci'));
    }
    
    
     /**
     * @Route("/hraci/pdf", name="pdf_players")
     */


    public function pdfPlayersAction(Request $request)
    {
      $pdf = $this->container->get("white_october.tcpdf")->create(
            'Portrait',
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'ISO-8859-1',
            false
        );
        $pdf->SetFont('dejavusans', '', 11, '', true);
         $pdf->SetTitle('hraci.pdf');
        $pdf->AddPage();

       
        $html = '<h1 style="text-align:center;">Hráči</h1> 
        <table>
              <thead>
               <tr>
                 <th>ID</th>
                 <th>JMÉNO</th>
                 <th>PŘÍJMENÍ</th>
                 <th>DATUM NAR.</th>
                 <th>ZEMĚ</th>
                 <th>VÝŠKA</th>
                 <th>VÁHA</th>
                  <th>DRŽENÍ HOLE</th>
                   <th>POZICE</th>
                    <th>TÝM</th>
                  
               </tr>
               </thead>';
            
       
                         
        $html .='</table>';

        
         $pdf->writeHTMLCell(
            $w = 0,
            $h = 0,
            $x = '',
            $y = '',
            $html,
            $border = 1,
            $ln = 2,
            $fill = 0,
            $reseth = true,
            $align = '',
            $autopadding = false
        );
        $pdf->Output("hraci.pdf", 'I');
    
    
}    


}
