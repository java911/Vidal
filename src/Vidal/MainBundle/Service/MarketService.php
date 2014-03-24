<?php
namespace Vidal\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\TwigBundle\TwigEngine as Templating;
use Symfony\Component\HttpFoundation\Session\Session;

class MarketService{

    protected $drugs;

    protected $session;

    public function __construct(){
        $this->session = new Session();
        if ( $this->session->get('basket')){
            $this->drugs = $this->session->get('basket');
        }else{
            $this->drugs = array();
        }
    }

    public function save(){
        $this->session->set('basket') = $this->drugs;
    }

    public function get($key){
        return $this->drugs[$key];
    }

    public function getAll(){
        return $this->drugs;
    }

    public function set($product){
        $this->drugs[$product->getCode()] = $product;
    }

    public function add($product){
        if ( isset($this->drugs[$product->getCode()])){
            $this->drugs[$product->getCode()]->addCount($product->getCount());
        }else{
            $this->drugs[$product->getCode()] = $product;
        }
    }

    public function remove(){
        unset($this->drugs[$product->getCode()]);
    }


}