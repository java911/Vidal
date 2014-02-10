<?php
namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
	/**
	 * @Route("/", name = "index")
	 * @Template("VidalDrugBundle:Index:index.html.twig")
	 */
	public function indexAction()
	{
		return array();
	}
}
