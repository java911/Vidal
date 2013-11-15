<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VidalController extends Controller
{
	/**
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", name="product", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function productAction($EngName, $DocumentID = null)
	{
		return array();
	}

	/**
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document_id", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function documentAction($DocumentID = null)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array();

		$document = $em->getRepository('VidalMainBundle:Document')->findById($DocumentID);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByDocumentID($DocumentID);
		$products  = $em->getRepository('VidalMainBundle:Product')->findByDocumentID($DocumentID);
		$infoPages = $em->getRepository('VidalMainBundle:InfoPage')->findByDocumentID($DocumentID);

		if (!empty($products)) {
			$productIds = array();
			foreach ($products as $product) {
				$productIds[] = $product['ProductID'];
			}

			$params['atcCodes']     = $em->getRepository('VidalMainBundle:ATC')->findByProducts($productIds);
			$params['owners']       = $em->getRepository('VidalMainBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalMainBundle:Company')->findDistributorsByProducts($productIds);
		}
		else {
			$params['atcCodes'] = $em->getRepository('VidalMainBundle:ATC')->findByDocumentID($DocumentID);
		}

		$params['document']  = $document;
		$params['molecules'] = $molecules;
		$params['products']  = $products;
		$params['infoPages'] = $infoPages;

		return $params;
	}

	/**
	 * @Route("poisk_preparatov/fir_{CompanyID}.{ext}", name="company", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function companyAction($CompanyID)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc", defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function atcAction($ATCCode)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/inf_{InfoPageID}.{ext}", name="inf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function infAction($InfoPageID)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/atc_{MoleculeID}.{ext}", name="act", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function actAction($MoleculeID)
	{
		return array();
	}
}
