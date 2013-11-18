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
	public function productAction($EngName, $ProductID = null)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array();

		$product = $em->getRepository('VidalMainBundle:Product')->findByProductID($ProductID);

		if (!$product) {
			throw $this->createNotFoundException();
		}

		$document = $em->getRepository('VidalMainBundle:Document')->findByProductDocument($ProductID);

		if ($document) {
			$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByProductID($ProductID);
		}
		else {
			$molecule = $em->getRepository('VidalMainBundle:Molecule')->findOneByProductID($ProductID);
			if (!$molecule) {
				throw $this->createNotFoundException();
			}
			$document = $em->getRepository('VidalMainBundle:Document')->findByMoleculeID($molecule['MoleculeID']);
			if (!$document) {
				throw $this->createNotFoundException();
			}
		}

		return array();
	}

	/**
	 * @Route("/poisk_preparator/{EngName}.{ext}", name="document_name", defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function documentAction($EngName, $DocumentID = null)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array();

		$document = $DocumentID
			? $em->getRepository('VidalMainBundle:Document')->findById($DocumentID)
			: $em->getRepository('VidalMainBundle:Document')->findByName($EngName);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		if (!$DocumentID) {
			$DocumentID = $document->getDocumentID();
		}

		$articleID = $document->getArticleID();

		$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByDocumentID($DocumentID);
		$products  = $articleID == 5
			? $em->getRepository('VidalMainBundle:Product')->findByMolecules($molecules)
			: $em->getRepository('VidalMainBundle:Product')->findByDocumentID($DocumentID);
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

		$params['articleId'] = $articleID;
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
	 * @Route("poisk_preparatov/act_{MoleculeID}.{ext}", name="molecule", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template
	 */
	public function moleculeAction($MoleculeID)
	{
		return array();
	}
}
