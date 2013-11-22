<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VidalController extends Controller
{
	/**
	 * @Route("poisk_preparatov/fir_{CompanyID}.{ext}", name="company", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalMainBundle:Vidal:company.html.twig")
	 */
	public function companyAction($CompanyID)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc", defaults={"ext"="htm"})
	 * @Template("VidalMainBundle:Vidal:atc.html.twig")
	 */
	public function atcAction($ATCCode)
	{
		$em  = $this->getDoctrine()->getManager();
		$atc = $em->getRepository('VidalMainBundle:ATC')->findOneByATCCode($ATCCode);

		if (!$atc) {
			throw $this->createNotFoundException();
		}

		# все продукты по ATC-коду и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalMainBundle:Product')->findByATCCode($ATCCode);
		$products    = array();
		foreach ($productsRaw as $product) {
			$key = $product['ProductID'];
			isset($products[$key]) && isset($product[$key]['ArticleID']) && $product['ArticleID'] < $products[$key]['ArticleID']
				? $products[$key][] = $product
				: $products[$key] = array($product);
		}

		# надо разбить на те, что с описанием и те, что без, а потом обе группы отсортировать по названию продукта
		$products1  = array();
		$products2  = array();
		$productIds = array();

		foreach ($products as $id => $product) {
			$product[0]['DocumentID']
				? $products1[] = $product[0]
				: $products2[] = $product[0];
			$productIds[] = $id;
		}

		uasort($products1, array($this, 'sortProducts'));
		uasort($products2, array($this, 'sortProducts'));

		# надо получить компании и сгруппировать их по продукту
		$companies        = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
		$productCompanies = array();

		foreach ($companies as $company) {
			$key = $company['ProductID'];
			isset($productCompanies[$key])
				? $productCompanies[$key][] = $company
				: $productCompanies[$key] = array($company);
		}

		return array(
			'atc'       => $atc,
			'products1' => $products1,
			'products2' => $products2,
			'companies' => $productCompanies
		);
	}

	/**
	 * @Route("poisk_preparatov/inf_{InfoPageID}.{ext}", name="inf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:inf.html.twig")
	 */
	public function infAction($InfoPageID)
	{
		return array();
	}

	/**
	 * @Route("poisk_preparatov/act_{MoleculeID}.{ext}", name="molecule", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:molecule.html.twig")
	 */
	public function moleculeAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager();
		$molecule = $em->getRepository('VidalMainBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		$document = $em->getRepository('VidalMainBundle:Document')->findByMoleculeID($MoleculeID);

		return array(
			'molecule' => $molecule,
			'document' => $document,
		);
	}

	/**
	 * @Route("poisk_preparatov/lact_{MoleculeID}.{ext}", name="molecule_included", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:molecule_included.html.twig")
	 */
	public function moleculeIncludedAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager();
		$molecule = $em->getRepository('VidalMainBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		$products = $em->getRepository('VidalMainBundle:Product')->findByMoleculeID($MoleculeID);

		return array(
			'molecule' => $molecule,
			'products' => $products,
		);
	}

	/**
	 * @Route("poisk_preparatov/gnp.{ext}", name="gnp", defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:gnp.html.twig")
	 */
	public function gnpAction()
	{
		return array();
	}

	/**
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", name="product", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalMainBundle:Vidal:document.html.twig")
	 */
	public function productAction($EngName, $ProductID)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array();

		$product = $em->getRepository('VidalMainBundle:Product')->findByProductID($ProductID);

		if (!$product) {
			throw $this->createNotFoundException();
		}

		$document  = $em->getRepository('VidalMainBundle:Document')->findByProductDocument($ProductID);
		$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByProductID($ProductID);

		if ($document) {
			$articleId = $document->getArticleID();

			if ($articleId == 1) {
				# описания активных веществ только для мономолекулярных препаратов
				if (count($molecules) != 1) {
					throw $this->createNotFoundException();
				}
				else {
					$params['molecule'] = $molecules[0];
				}
			}
			else {
				$params['molecules'] = $molecules;
			}

			$params['document']  = $document;
			$params['articleId'] = $articleId;
			$params['infoPages'] = $em->getRepository('VidalMainBundle:InfoPage')->findByDocumentID($document->getDocumentID());
		}
		else {
			# если связи ProductDocument не найдено, то это описание конкретного вещества (Molecule)
			$molecule = $em->getRepository('VidalMainBundle:Molecule')->findOneByProductID($ProductID);

			if ($molecule) {
				$document = $em->getRepository('VidalMainBundle:Document')->findByMoleculeID($molecule['MoleculeID']);

				if (!$document) {
					throw $this->createNotFoundException();
				}

				$params['document']  = $document;
				$params['molecule']  = $molecule;
				$params['articleId'] = $document->getArticleId();
				$params['infoPages'] = $em->getRepository('VidalMainBundle:InfoPage')->findByDocumentID($document->getDocumentID());
			}
		}

		$productIds             = array($product['ProductID']);
		$params['product']      = $product;
		$params['products']     = array($product);
		$params['molecules']    = $molecules;
		$params['atcCodes']     = $em->getRepository('VidalMainBundle:ATC')->findByProducts($productIds);
		$params['owners']       = $em->getRepository('VidalMainBundle:Company')->findOwnersByProducts($productIds);
		$params['distributors'] = $em->getRepository('VidalMainBundle:Company')->findDistributorsByProducts($productIds);

		return $params;
	}

	/**
	 * @Route("/poisk_preparatov/{EngName}.{ext}", name="document_name", defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalMainBundle:Vidal:document.html.twig")
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
		else {
			$params['documentId'] = $document->getDocumentID();
		}

		$articleId = $document->getArticleID();

		$molecules = $em->getRepository('VidalMainBundle:Molecule')->findByDocumentID($DocumentID);
		$products  = $articleId == 1
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

		$params['articleId'] = $articleId;
		$params['document']  = $document;
		$params['molecules'] = $molecules;
		$params['products']  = $products;
		$params['infoPages'] = $infoPages;

		return $params;
	}

	private function sortProducts($a, $b)
	{
		if ($a['RusName'] == $b['RusName']) {
			return 0;
		}

		return $a['RusName'] > $b['RusName'] ? 1 : -1;
	}
}
