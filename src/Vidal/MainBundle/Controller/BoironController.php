<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BoironController extends Controller
{

	/**
	 * @Route("/boiron", name="boiron_main")
	 * @Template("VidalMainBundle:Boiron:index.html.twig")
	 */
	public function indexAction()
	{
		return array(
			'title'       => 'Boiron  - Французская помощь при ОРВИ',
			'description' => 'Boiron  - Французская помощь при ОРВИ',
			'keywords'    => 'Boiron, Французская помощь при ОРВИ',
			'noYad'       => true,
			'menu_left'   => 'boiron',
		);
	}

	/**
	 * @Route("/boiron/{pageNum}", name="boiron_page")
	 * @Template("VidalMainBundle:Boiron:page.html.twig")
	 */
	public function pageAction($pageNum)
	{
		return array(
			'title'       => 'Boiron - Французская помощь при ОРВИ',
			'description' => 'Boiron - Французская помощь при ОРВИ',
			'keywords'    => 'Boiron - Французская помощь при ОРВИ',
			'noYad'       => true,
			'menu_left'   => 'boiron - Французская помощь при ОРВИ',
			'page'		  => $pageNum
		);
	}
}