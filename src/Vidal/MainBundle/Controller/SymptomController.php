<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Vidal\MainBundle\Entity\DiseaseParty;

class SymptomController extends Controller{

    /**
     * @Route("/symptoms", name="symptom_page")
     * @Template("VidalMainBundle:Disease:page.html.twig")
     */
    public function pageSymptomAction(){
        return array();
    }

    /**
     * @Route("/symptom/{party}", name="render_symptom", defaults = { "party"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:symptomList.html.twig")
     */
    public function SymptomListAction($party = 'all'){
        $party = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseParty')->findOneById($party);
        $symptoms = $party->getSymptoms();
        return array(
            'symptoms'   => $symptoms,
        );
    }

    /**
     * @Route("/symptom-body/{sex}/{party}", name="symptom_body", defaults = { "sex"="1" , "party"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:body.html.twig")
     */
    public function bodyAction($sex = 1, $party = 'all'){
        return array(
            'sex'   => $sex,
            'party'   => $party,
        );
    }

}