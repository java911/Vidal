<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Vidal\MainBundle\Market\Drug;
use Vidal\MainBundle\Market\FindDrug;

class MarketController extends Controller{

//    public function AddToBasket(){}
//
//    public function setToBasket(){}
//
//    public function removeToBasket(){}
//
//    public function basketList(){}

    /**
     * @Route("/drug-list/{title}", name="drug_list",  options={"expose"=true})
     */
    public function productListAction( $title = '' ){
//        $findDrug = $this->get('findDrug.service');
        $em = $this->getDoctrine()->getManager();
        $findDrug = new FindDrug($em,$title);
        $findDrug->setId( 14 );
        $findDrug->isDocument( false );
        $body = $findDrug->run();

        return new Response($body);
    }

}
