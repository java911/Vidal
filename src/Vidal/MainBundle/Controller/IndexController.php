<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
	const PUBLICATIONS_INDEX_PAGE = 7;

	/**
	 * @Route("/", name="index")
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array(
			'indexPage' => true,
			'seotitle'  => 'Справочник лекарственных препаратов Видаль. Описание лекарственных средств',
		);

		$params['publicationsPagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalMainBundle:Publication')->getQueryEnabled(),
			$request->query->get('p', 1),
			self::PUBLICATIONS_INDEX_PAGE
		);

		return $params;
	}

	/**
	 * @Route("/otvety_specialistov", name="qa")
	 * @Template()
	 */
	public function qaAction()
	{
		return array(
			'title'           => 'Вопрос-ответ',
			'menu_left'       => 'qa',
			'questionAnswers' => $this->getDoctrine()->getRepository('VidalMainBundle:QuestionAnswer')->findAll(),
		);
	}

    /**
     * @Route("/pharmacies-map", name="pharmacies_map")
     * @Template("VidalMainBundle:Index:map.html.twig")
     */
    public function pharmaciesMapAction(){
        return array();
    }
}
