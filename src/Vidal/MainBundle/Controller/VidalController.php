<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VidalController extends Controller
{
	const PRODUCTS_PER_PAGE = 40;

	/**
	 * Список препаратов по компании
	 * @Route("poisk_preparatov/fir_{CompanyID}.{ext}", name="company", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("poisk_preparatov/lfir_{CompanyID}.{ext}", name="company_products", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:company.html.twig")
	 */
	public function companyAction($CompanyID)
	{
		$em      = $this->getDoctrine()->getManager();
		$company = $em->getRepository('VidalMainBundle:Company')->findByCompanyID($CompanyID);

		if ($company == null) {
			throw $this->createNotFoundException();
		}

		$productsRaw = $em->getRepository('VidalMainBundle:Product')->findByOwner($CompanyID);

		# находим представительства
		$productsRepresented = array();
		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['InfoPageID'];
			if (!empty($key) && !isset($productsRepresented[$key])) {
				$productsRepresented[$key] = $productsRaw[$i];
			}
		}

		# отсеиваем дубли
		$products = array();

		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];
			if (!isset($productsRaw[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		# надо разбить на те, что с представительством и описанием(2,5) и остальные
		$products1 = array();
		$products2 = array();

		foreach ($products as $id => $product) {
			if ($product['InfoPageID'] && ($product['ArticleID'] == 2 || $product['ArticleID'] == 5)) {
				$key = $product['DocumentID'];
				if (!isset($products1[$key])) {
					$products1[$key] = $product;
				}
			}
			else {
				$products2[] = $product;
			}
		}

		return array(
			'company'             => $company,
			'productsRepresented' => $productsRepresented,
			'products1'           => $products1,
			'products2'           => $products2,
		);
	}

	/**
	 * Список препаратов по коду АТХ
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc", options={"expose":true}, defaults={"ext"="htm"})
	 *
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

		if (empty($productsRaw)) {
			return array('atc' => $atc);
		}

		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];
			if (!isset($productsRaw[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		# надо разбить на те, что с описанием(2,5) и остальные
		$products1  = array();
		$products2  = array();
		$productIds = array();

		foreach ($products as $id => $product) {
			if ($product['ArticleID'] == 2 || $product['ArticleID'] == 5) {
				$key = $product['DocumentID'];
				if (!isset($products1[$key])) {
					$products1[$key] = $product;
				}
			}
			else {
				$products2[] = $product;
			}

			$productIds[] = $id;
		}

		return array(
			'atc'       => $atc,
			'products1' => $products1,
			'products2' => $products2,
			'companies' => $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds)
		);
	}

	/**
	 * Список препаратов по клиннико-фармакологической группе
	 * @Route("poisk_preparatov/cl-ph-group/{description}", name="clphgroup")
	 * @Template("VidalMainBundle:Vidal:clphgroup.html.twig")
	 */
	public function clphgroupAction($description)
	{
		$em       = $this->getDoctrine()->getManager();
		$products = $em->getRepository('VidalMainBundle:Product')->findByClPhGroup($description);
		$params   = array('products' => $products, 'description' => $description);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список препаратов по фармако-терапевтической группе
	 * @Route("poisk_preparatov/ph-th-group/{id}", name="phthgroup", defaults={"id":"\d+"})
	 * @Template("VidalMainBundle:Vidal:phthgroup.html.twig")
	 */
	public function phthgroupAction($id)
	{
		$em        = $this->getDoctrine()->getManager();
		$phthgroup = $em->getRepository('VidalMainBundle:PhThGroups')->findById($id);

		if ($phthgroup === null) {
			throw $this->createNotFoundException();
		}

		$products = $em->getRepository('VidalMainBundle:Product')->findByPhThGroup($id);
		$params   = array('phthgroup' => $phthgroup, 'products' => $products);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список препаратов и активных веществ по показанию (Nozology)
	 * @Route("poisk_preparatov/lno_{NozologyCode}", name="nozology_code")
	 * @Template("VidalMainBundle:Vidal:nozology_code.html.twig")
	 */
	public function nozologyCodeAction($NozologyCode)
	{
		$em = $this->getDoctrine()->getManager();

		if ($pos = strpos($NozologyCode, '.htm')) {
			$NozologyCode = substr($NozologyCode, $pos, 4);
		}

		$nozology = $em->getRepository('VidalMainBundle:Nozology')->findByCode($NozologyCode);

		if ($nozology === null) {
			throw $this->createNotFoundException();
		}

		$documents = $em->getRepository('VidalMainBundle:Document')->findByNozologyCode($NozologyCode);
		$params    = array('nozology' => $nozology);

		if (!empty($documents)) {
			$params['molecules'] = $em->getRepository('VidalMainBundle:Molecule')->findByDocuments1($documents);
			$products1           = $em->getRepository('VidalMainBundle:Product')->findByDocuments25($documents);
			$products2           = $em->getRepository('VidalMainBundle:Product')->findByDocuments4($documents);
			$products            = array();

			# надо слить продукты, исключая повторения и отсортировать по названию
			foreach ($products1 as $id => $product) {
				$products[] = $product;
			}
			foreach ($products2 as $id => $product) {
				if (!isset($products1[$id])) {
					$products[] = $product;
				}
			}
			usort($products, function ($a, $b) {
				return strcmp($a['RusName'], $b['RusName']);
			});

			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Страничка представительства и список препаратов
	 * @Route("poisk_preparatov/inf_{InfoPageID}.{ext}", name="inf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("poisk_preparatov/linf_{InfoPageID}.{ext}", name="linf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:inf.html.twig")
	 */
	public function infAction($InfoPageID)
	{
		$em       = $this->getDoctrine()->getManager();
		$infoPage = $em->getRepository('VidalMainBundle:InfoPage')->findByInfoPageID($InfoPageID);

		if (!$infoPage) {
			throw $this->createNotFoundException();
		}

		$picture     = $em->getRepository('VidalMainBundle:Picture')->findByInfoPageID($InfoPageID);
		$params      = array('infoPage' => $infoPage, 'picture' => $picture);
		$documentIds = $em->getRepository('VidalMainBundle:Document')->findIdsByInfoPageID($InfoPageID);

		if (!empty($documentIds)) {
			$products = $em->getRepository('VidalMainBundle:Product')->findByDocumentIDs($documentIds);

			if (!empty($products)) {
				$productIds          = $this->getProductIds($products);
				$params['products']  = $products;
				$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
			}
		}

		return $params;
	}

	/**
	 * Список препаратов по активному веществу: одно-монокомпонентные
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
	 * Отображение списка препаратов, в состав которых входит активное вещество (Molecule)
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

		# все продукты по активному веществу и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalMainBundle:Product')->findByMoleculeID($MoleculeID);

		if (empty($productsRaw)) {
			return array('molecule' => $molecule);
		}

		$products   = array();
		$productIds = array();

		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];

			if (!isset($products[$key])) {
				$products[$key] = $productsRaw[$i];
				$productIds[]   = $key;
			}
		}

		# препараты надо разбить на монокомнонентные и многокомпонентные группы
		$components = $em->getRepository('VidalMainBundle:Molecule')->countComponents($productIds);
		$products1  = array();
		$products2  = array();

		foreach ($products as $id => $product) {
			$components[$id] == 1
				? $products1[$id] = $product
				: $products2[$id] = $product;
		}

		uasort($products1, array($this, 'sortProducts'));
		uasort($products2, array($this, 'sortProducts'));

		return array(
			'molecule'  => $molecule,
			'products1' => $products1,
			'products2' => $products2,
			'companies' => $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds),
		);
	}

	/**
	 * Страничка рассшифровки МНН аббревиатур
	 * @Route("poisk_preparatov/gnp.{ext}", name="gnp", defaults={"ext"="htm"})
	 *
	 * @Template("VidalMainBundle:Vidal:gnp.html.twig")
	 */
	public function gnpAction()
	{
		return array();
	}

	/**
	 * Отображение препаратов или бадов по букве алфавита
	 * @Route("/drugs", name="drugs_by_letter")
	 *
	 * @Template("VidalMainBundle:Vidal:drugs_by_letter.html.twig")
	 */
	public function drugsByLetterAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$t  = $request->query->get('t', 'p'); // тип препараты-бады-вместе
		$p  = $request->query->get('p', 1); // номер страницы
		$l  = $request->query->get('l', null); // буква
		$n  = $request->query->has('n'); // только безрецептурные препараты

		$params = array(
			't' => $t,
			'p' => $p,
			'l' => $l,
			'n' => $n,
		);

		if ($l != null) {
			$paginator  = $this->get('knp_paginator');
			$pagination = $paginator->paginate(
				$em->getRepository('VidalMainBundle:Product')->getQueryByLetter($l, $t, $n),
				$p,
				self::PRODUCTS_PER_PAGE
			);

			$products             = $pagination->getItems();
			$params['pagination'] = $pagination;

			if (!empty($products)) {
				$productIds = array();

				foreach ($products as $product) {
					$productIds[] = $product->getProductID();
				}

				$params['products']    = $products;
				$params['indications'] = $em->getRepository('VidalMainBundle:Document')->findIndicationsByProductIds($productIds);
				$params['companies']   = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
				$params['pictures']    = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
			}
		}

		return $params;
	}

	/**
	 * Описание препарата
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", name="product", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
	 *
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

		if ($params['document']) {
			$params['nozologies'] = $em->getRepository('VidalMainBundle:Nozology')->findByDocumentID($document->getDocumentID());
		}

		$productIds             = array($product['ProductID']);
		$params['product']      = $product;
		$params['products']     = array($product);
		$params['molecules']    = $molecules;
		$params['atcCodes']     = $em->getRepository('VidalMainBundle:ATC')->findByProducts($productIds);
		$params['owners']       = $em->getRepository('VidalMainBundle:Company')->findOwnersByProducts($productIds);
		$params['distributors'] = $em->getRepository('VidalMainBundle:Company')->findDistributorsByProducts($productIds);
		$params['pictures']     = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		$params['phthgroups']   = $em->getRepository('VidalMainBundle:PhThGroups')->findByProductId($ProductID);

		return $params;
	}

	/**
	 * Описание по документу и отображение информации по препаратам или веществу
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}.{ext}", name="document_name", defaults={"ext"="htm"})
	 *
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

		$products = $articleId == 1
			? $em->getRepository('VidalMainBundle:Product')->findByMolecules($molecules)
			: $em->getRepository('VidalMainBundle:Product')->findByDocumentID($DocumentID);

		if (empty($products)) {
			$products = $em->getRepository('VidalMainBundle:Product')->findByMolecules($molecules);
		}

		if (!empty($products)) {
			$productIds             = $this->getProductIds($products);
			$params['atcCodes']     = $em->getRepository('VidalMainBundle:ATC')->findByProducts($productIds);
			$params['owners']       = $em->getRepository('VidalMainBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalMainBundle:Company')->findDistributorsByProducts($productIds);
			$params['pictures']     = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		}
		else {
			$params['atcCodes'] = $em->getRepository('VidalMainBundle:ATC')->findByDocumentID($DocumentID);
			$params['pictures'] = array();
		}

		$params['nozologies'] = $em->getRepository('VidalMainBundle:Nozology')->findByDocumentID($DocumentID);
		$params['articleId']  = $articleId;
		$params['document']   = $document;
		$params['molecules']  = $molecules;
		$params['products']   = $products;
		$params['infoPages']  = $em->getRepository('VidalMainBundle:InfoPage')->findByDocumentID($DocumentID);

		return $params;
	}

	/** Получить массив идентификаторов продуктов */
	private function getProductIds($products)
	{
		$productIds = array();

		foreach ($products as $product) {
			$productIds[] = $product['ProductID'];
		}

		return $productIds;
	}

	/** Отсортировать препараты по имени */
	private function sortProducts($a, $b)
	{
		return strcasecmp($a['RusName'], $b['RusName']);
	}
}
