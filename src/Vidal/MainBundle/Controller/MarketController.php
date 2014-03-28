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
        if ( $isDocs == false ){
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Product')->findOneBy(array('ProductID' => $drugId));
        }else{
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Document')->findOneBy(array('DocumentID' => $drugId));
        }
        if ($drug){
            $RusName = $drug->getRusName();

            $p    = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
            $r    = array('', '');

            $title = preg_replace($p, $r, $RusName);
            $first = mb_substr($title,0,2);//первая буква
            $last = mb_substr($title,2);//все кроме первой буквы
            $last = mb_strtolower($last,'UTF-8');
            $title =$first.$last;

            $list = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->find($title);

            $lists = array();
            foreach ( $list as $drug){
                $lists[$drug->getGroupApt()][] = $drug;
            }
        }else{
            $lists = array();
        }


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
