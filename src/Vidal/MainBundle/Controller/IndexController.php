<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
	const PUBLICATIONS_INDEX_PAGE = 6;
	const PUBLICATIONS_PER_PAGE   = 25;

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
	 * @Route("/novosti/novosti_{id}.{ext}", name="publication_old", defaults={"ext"="html"})
	 * @Route("/novosti/{id}", name="publication")
	 * @Template()
	 */
	public function publicationAction($id)
	{
		$publication = $this->getDoctrine()->getRepository('VidalMainBundle:Publication')->findOneById($id);

		if (!$publication) {
			throw $this->createNotFoundException();
		}

		return array(
			'publication' => $publication,
			'menu_left'   => 'news',
			'title'       => $this->strip($publication->getTitle()) . ' | Новости',
		);
	}

	/**
	 * @Route("/novosti", name="news")
	 * @Template()
	 */
	public function newsAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array(
			'menu_left' => 'news',
			'title'     => 'Новости',
		);

		$params['publicationsPagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalMainBundle:Publication')->getQueryEnabled(),
			$request->query->get('p', 1),
			self::PUBLICATIONS_PER_PAGE
		);

		return $params;
	}

	/**
	 * @Route("/qa", name="qa")
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

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}
}
