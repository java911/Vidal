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

	public function renderAction($groupId)
	{

        #@todo Проверить перед заливкой
        $o = array( 'charset' => 'utf-8' );
        $geo = new Geo($o);


        $city =     $geo->get_value('city', true);
        $country =  $this->getDoctrine()->getRepository('VidalMainBundle:Country')->findOneByShortTitle($geo->get_value('country', true))->getTitle();
        $ar = $geo->get_value();
//        $country = '1';
//        $city = '1';

        $banner = $this->getDoctrine()->getRepository('VidalMainBundle:Banner')->findByGroup($groupId, $country, $city);

		return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'banner' => $banner
		));
	}
}
