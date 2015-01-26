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
	 * @Route("/boiron")
	 * @Template("VidalMainBundle:Boiron:index.html.twig")
	 */
	public function indexAction()
	{
		return array(
			'title'       => 'Коллост - инновационный стерильный биопластический коллагеновый материал',
			'description' => 'Коллост - инновационный стерильный биопластический коллагеновый материал, обеспечивающий регенерацию пораженных тканей',
			'keywords'    => 'инновационный стерильный биопластический коллагеновый материал регенерация пораженных тканей',
			'noYad'       => true,
			'menu_left'   => 'boiron',
		);
	}
}