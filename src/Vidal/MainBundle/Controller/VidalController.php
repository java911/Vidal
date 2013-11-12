<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VidalController extends Controller
{
	/**
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.htm", name="product")
	 *
	 * @Template
	 */
	public function productAction($EngName, $DocumentID = null)
	{
		return array();
	}

	/**
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.htm", name="document_id", requirements={"DocumentID":"\d+"})
	 *
	 * @Template
	 */
	public function documentAction($DocumentID = null)
	{
		$em = $this->getDoctrine()->getManager();

		$document = $em->getRepository('VidalMainBundle:Document')->findById($DocumentID);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByDocumentID($DocumentID);
		$products  = $em->getRepository('VidalMainBundle:Product')->findByDocumentID($DocumentID);
		$atc       = $em->getRepository('VidalMainBundle:ATC')->findByDocumentID($DocumentID);

		return array(
			'document'  => $document,
			'molecules' => $molecules,
			'products'  => $products,
			'atc'       => $atc,
		);
	}

	/**
	 * @Route("poisk_preparatov/fir_{CompanyID}.htm", name="company")
	 *
	 * @Template
	 */
	public function companyAction($CompanyID)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/lat_{ATCCode}.htm", name="atc")
	 *
	 * @Template
	 */
	public function atcAction($ATCCode)
	{
		return array();
	}
}
