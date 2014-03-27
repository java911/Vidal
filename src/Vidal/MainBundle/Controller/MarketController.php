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
use Vidal\MainBundle\Market\Market;

class MarketController extends Controller{

    /**
     * @Route("/add-to-basket/{productId}/{group}/{count}", name="drug_list", defaults={"count"="1"}, options={"expose"=true})
     * @Template("VidalMainBundle:list.html.twig")
     */
    public function AddToBasket($productId, $count){

//        $product = $this

//        $product =

        $market = new Market();
        $product = $market->get($productId);
        $market->set($product);
        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }
//
    public function setToBasket($productId,$count){
        $market = new Market();
        $product = $market->get($productId);
        $market->set($product);
        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }
//
    public function removeToBasket($productId){
        $market = new Market();
        $product = $market->get($productId);
        $market->remove($product);
        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }
//    public function basketList(){}

    /**
     * @Route("/drug-list/{drugId}/{isDocs}", name="drug_list", defaults={"isDocs"="false"}, options={"expose"=true})
     * @Template("VidalMainBundle:list.html.twig")
     */
    public function productListAction( $drugId, $isDocs = false ){
        $body = $this->getDoctrine()->getRepository('VidalMainBundle:MarketCache')->findOneBy(array('target' => $drugId, 'document' => $isDocs));
        if ( $body == null){
            $body = '';
        }

//        $findDrug = $this->get('findDrug.service');
//        $em = $this->getDoctrine()->getManager();
//        $findDrug = new FindDrug($em,$title);
//        $findDrug->setId( 2 );
//        $findDrug->isDocument( false );
//        $body = $findDrug->run();

        return array('body' => $body);
    }

}
