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

        $o = array( 'charset' => 'utf-8' );
        $geo = new Geo($o);

        # этот метод позволяет получить все данные по ip в виде массива.
        # массив имеет ключи 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
        $data = $geo->get_value();
        $city =     $geo->get_value('city', true);
        $country =  $this->getDoctrine()->getRepository('EvrikaMainBundle:Country')->findOneByShortTitle($geo->get_value('country', true))->getTitle();
        $ar = $geo->get_value();

		return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'banner' => $this->getDoctrine()->getRepository('VidalMainBundle:Banner')->findByGroup($groupId)
		));
	}
}
