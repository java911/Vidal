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
    public function symptomListAction($party = 'all'){
        if ($party == 'all'){
            $symptoms = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseSymptom')->findAll();
        }else{
            $party = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseParty')->findOneById($party);
            $symptoms = $party->getSymptoms();
        }
        return array(
            'symptoms'   => $symptoms,
        );
    }

    /**
     * @Route("/diseases/{symptom}", name="render_disease", defaults = { "symptom"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:diseaseList.html.twig")
     */
    public function diseaseListAction($symptom = 'all'){
        if ($symptom == 'all'){
            $diseases = $this->getDoctrine()->getRepository('VidalMainBundle:Disease')->findAll();
        }else{
            $symptom = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseSymptom')->findOneById($symptom);
            $diseases = $symptom->getDiseases();
        }
        return array(
            'disease'   => $diseases,
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