<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SymptomController extends Controller{

    /**
     * @Route("/symptoms", name="symptom_page")
     * @Template("VidalMainBundle:Disease:page.html.twig")
     */
    public function pageSymptomAction(){
        return array();
    }

    /**
     * @Route("/symptom/{type}/{id}", name="symptom_ajax")
     * @Template()
     */
    public function ajaxSymptomAction($type,$id){}

}