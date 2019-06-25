<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use AppBundle\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');

    }

    /**
     * @Route("/cv61", name="cv61")
     */

    public function cv61Action(Request $request){
        $jmeno=$request->get('jmeno');
        $prijmeni=$request->get('prijmeni');
        return new Response("Dobry den ".$jmeno." ".$prijmeni);
    }

    /**
     * @Route("/cv62", name="cv62")
     */

    public function cv62Action(Request $request){
        return $this->render('default/index.html.twig');
    }



    /**
     * @Route("/cv63", name="cv63")
     */

    public function cv63Action(){
        $text='WELCOME';
        dump($text);
        return $this->render('default/cv63.html.twig',array('text'=>$text));
    }

    /**
     * @Route("/cv71", name="posli")
     */
    public function cv71Action()
    {
	$text='WELCOME';
        
        return $this->render('default/cv71.html.twig',array('text'=>$text));

       
            
           
        }

     /**
     * @Route("/cv72", name="poslivpoli")
     */
    public function cv72Action()
    {
    $jmeno='Kazimir';
    return $this->render('default/cv72.html.twig',['jmena'=>array('Karel','Josef','Jan')]);

          
        }

 /**
     * @Route("/cv73", name="poslipolevpoli")
     */
    public function cv73Action()
{
    $zamestnanec='Kazimir Lezak';
    return $this->render('default/cv73.html.twig',['zamestnanci'=>array(
        array('jmeno'=>'Karel','prijmeni'=>'Cil'),
        array('jmeno'=>'Josef','prijmeni'=>'Konec'),
	array('jmeno'=>'Jan','prijmeni'=>'Stred'),

    )]);
}

/**
     * @Route("/formular", name="cv8")
     */
    public function formularAction(Request $request)
    {

        $task = new Task;
        $form = $this->createFormBuilder($task)
            ->add("jmeno", TextType::class, array('label' => 'Zadej jmeno:   '))
             ->add("prijmeni", TextType::class, array('label' => 'Zadej prijmeni:   '))
		->add("pohlavi", ChoiceType::class,[
    'choices'  => [
        'Muz' => 'muz',
        'Zena' => 'zena',
          ]])

              ->add("odeslat", SubmitType::class, array('label' => 'Odeslat'))
            ->getForm();
          
        $form->handleRequest($request);  

    if ($form->isSubmitted() && $form->isValid()) {
          
        $jm = $task->getJmeno();
        $prijm = $task->getPrijmeni();
	$pohlavi = $task->getPohlavi();
        return new Response("Dobry den ".$jm." ".$prijm." a vase pohlavi je ".$pohlavi);

        return $this->redirectToRoute('formular');
           }
           return $this->render('default/form.html.twig',array('formular' => $form->createView(),));

           
        }

    /**
     * @Route("/sortiment", name="sortiment")
     */
     public function SortimentAction(Request $request){
        $sortiment = $this->getDoctrine()
                    ->getRepository("AppBundle:sklad")
                    ->findAll();
        return $this->render('default/sortiment.html.twig', array('sklad'=> $sortiment,));
     
     }

    /**
     * @Route("/ofirme", name="ofirme")
     */
    public function OfimeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/ofirme.html.twig');

    }


     /**
     * @Route("/databaze", name="databaze")
     */
    public function DatAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/databaze.html.twig');

    }
    
    /**
     * @Route("/kontakt", name="kontakt")
     */
    public function KontaktAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/kontakt.html.twig');

    }
    
    /**
     * @Route("/home", name="home")
     */
    public function HomeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('main.html.twig');

    }

    /**
     * @Route("/pdf", name="pdf")
     */


    public function pdfAction(Request $request)
    {
       $pdf = $this->container->get("white_october.tcpdf")->create(
            'Portrait',
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $html = '<h1>Delam na Symfony</h1>';

        $pdf->write(0,'Ahoj svete');
        $pdf->Output("hellow_world.pdf", 'I');
    
}    

       
  

}
