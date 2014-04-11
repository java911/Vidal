<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataController extends Controller
{
	protected $ids = array();

	/**
	 * @Route("/tt", name="data_products")
	 */
	public function ttAction()
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$subs = $em->getRepository('VidalDrugBundle:Subdivision')->findVracham();

		$this->populate($subs);

		echo '(' . implode(',', $this->ids) . ')'; exit;
	}

	private function populate($subs)
	{
		foreach ($subs as $sub) {
			$this->ids[] = $sub->getId();
			$children    = $sub->getChildren();

			if (!empty($children)) {
				$this->populate($children);
			}
		}
	}
}
