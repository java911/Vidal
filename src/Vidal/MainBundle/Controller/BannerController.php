<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vidal\MainBundle\Geo\Geo;

class BannerController extends Controller
{
	/**
	 * Добавить клик по банеру
	 * @Route("/banner-clicked/{bannerId}", name="banner_clicked", options={"expose"=true})
	 */
	public function bannerClickedAction($bannerId)
	{
		$this->getDoctrine()
			->getRepository('VidalMainBundle:Banner')
			->countClick($bannerId);

		return new Response();
	}

	public function renderAction($groupId, $testing = false)
	{
        if ($this->getUser()){
            $banners = $this->getDoctrine()->getRepository('VidalMainBundle:Banner')->findByGroup($groupId,$this->getUser());
        }else{
            $banners = $this->getDoctrine()->getRepository('VidalMainBundle:Banner')->findByGroup($groupId,$this->getUser());
        }
        return $this->render('VidalMainBundle:Banner:render.html.twig', array(
            'banner'  => $banners,
            'testing' => $testing,
        ));
	}

    /**
     * @Route("/get-popup", name="get-popup", options={"expose"=true})
     * @Template()
     */
    public function getPopupAction(){
        $popup = $this->getDoctrine()->getRepository('VidalMainBundle:Popup')->findPopup();
        $array = array('data'=>array(
            'img'  => $popup->getImage()['path'],
            'link' => $popup->getLink()
        ));
        $array = json_encode($array);
        echo $array;
        exit;

    }
}
