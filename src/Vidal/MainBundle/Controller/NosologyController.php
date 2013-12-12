<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NosologyController extends Controller
{
	/**
	 * @Route("/nosology/letter/{letter}", name="nosology_letter")
	 * @Route("/nosology", name="nosology")
	 * @Template("VidalMainBundle:Nosology:nosology.html.twig")
	 */
	public function nosologyAction($letter = null)
	{
		# поиск по букве
		$params = array();

		if ($letter) {
			$em = $this->getDoctrine()->getManager();
			$params['letter'] = $letter;
			$params['nosologies'] = $em->getRepository('VidalMainBundle:Nozology')->findByLetter($letter);
		}

		return $params;
	}
}
