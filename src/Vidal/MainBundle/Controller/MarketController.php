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
use Vidal\MainBundle\Market\Basket;

class MarketController extends Controller{

    /**
     * @Route("/add-to-basket/{code}/{count}", name="add_to_basket", defaults={"count"="1"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function AddToBasket($code, $count = 1){

        $basket = new Basket();

        $product = null;
        $product = $basket->getProduct($code);
        if ($product != null ){
            $product->setCount($product->getCount()+$count);
        }else{
            $pr = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->findOneByCode($code);
            if ($pr != null){
                $product = new Product();
                $product->setCount($count);
                $product->setTitle($pr->getTitle());
                $product->setCode($pr->getCode());
                $product->setGroup($pr->getGroup());
                $product->setPrice($pr->getPrice());
            }
        }
        if ($product != null){
            $basket->setProduct($product);
            $basket->save();
        }

        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/set-to-basket/{code}/{count}", name="set_to_basket", defaults={"count"="1"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function setToBasket($code, $count = 1){
        $basket = new Basket();

        $product = null;
        $product = $basket->getProduct($code);
        if ($product != null ){
            $product->setCount($count);
        }else{
            $pr = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->findOneByCode($code);
            if ($pr != null){
                $product = new Product();
                $product->setCount($count);
                $product->setTitle($pr->getTitle());
                $product->setCode($pr->getCode());
                $product->setGroup($pr->getGroup());
                $product->setPrice($pr->getPrice());
            }
        }
        if ($product != null){
            $basket->setProduct($product);
            $basket->save();
        }

        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/remove-to-basket/{code}", name="remove_to_basket", options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function removeToBasket($code){
        $basket = new Basket();
        $product = $basket->get($code);
        $basket->remove($product);
        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/drug-list/{drugId}/{isDocs}", name="drug_list", defaults={"isDocs"="false"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function productListAction( $drugId, $isDocs = false ){
        $cache = $this->getDoctrine()->getRepository('VidalMainBundle:MarketCache')->findOneBy(array('target' => $drugId, 'document' => $isDocs));
        $lists = $cache->getDrugs();

        return array('lists' => $lists);
    }

    /**
     * @Route("/basket-list", name="basket_list" )
     * @Template("VidalMainBundle:Market:basket_list.html.twig")
     */
    public function basketListAction(){
        return array();
    }
}
