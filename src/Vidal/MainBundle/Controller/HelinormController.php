<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class HelinormController extends Controller
{

	/**
	 * @Route("/helinorm" ,name="helinorm")
	 * @Template("VidalMainBundle:Helinorm:index.html.twig")
	 */
	public function indexAction()
	{
		return array(
			'title'       => 'Helinorm',
			'description' => 'Helinorm',
			'keywords'    => 'Helinorm',
			'noYad'       => true,
			'menu_left'   => 'helinorm',
		);
	}
}