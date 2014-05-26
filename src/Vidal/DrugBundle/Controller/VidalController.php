<?php
namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VidalController extends Controller
{
	const PRODUCTS_PER_PAGE  = 40;
	const COMPANIES_PER_PAGE = 50;
	const MOLECULES_PER_PAGE = 50;

	/** @Route("/poisk_preparatov/", name="r1") */
	public function r1()
	{
		return $this->redirect($this->generateUrl('searche'), 301);
	}

	/**
	 * Список компаний
	 *
	 * @Route("/drugs/firms", name="firms")
	 * @Template("VidalDrugBundle:Vidal:firms.html.twig")
	 */
	public function firmsAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$q  = $request->query->get('q', null);
		$l  = $request->query->get('l', null);
		$p  = $request->query->get('p', 1);

		//		$companies = $em->getRepository('VidalDrugBundle:Company')->getQuery()->getResult();
		//		$letters   = array();
		//		foreach ($companies as $company) {
		//			$letter = mb_strtoupper(mb_substr($company->getLocalName(), 0, 1, 'utf-8'), 'utf-8');
		//			if (!isset($letters[$letter])) {
		//				$letters[$letter] = '';
		//			}
		//		}
		//		var_dump($letters);
		//		exit;

		if ($l) {
			$query = $em->getRepository('VidalDrugBundle:Company')->getQueryByLetter($l);
		}
		elseif ($q) {
			$query = $em->getRepository('VidalDrugBundle:Company')->findByQueryString($q);
		}
		else {
			$query = $em->getRepository('VidalDrugBundle:Company')->getQuery();
		}

		$params = array(
			'menu_drugs' => 'firms',
			'title'      => 'Фирмы-производители',
			'q'          => $q,
			'l'          => $l,
			'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::COMPANIES_PER_PAGE),
		);

		return $params;
	}

	/**
	 * Список препаратов по компании
	 *
	 * @Route("/drugs/firm/{CompanyID}", name="firm_item", requirements={"CompanyID":"\d+"})
	 * @Route("/poisk_preparatov/fir_{CompanyID}.{ext}", name="firm_item_old", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/lfir_{CompanyID}.{ext}", name="firm_products_old", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:firm_item.html.twig")
	 */
	public function firmItemAction($CompanyID)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$company = $em->getRepository('VidalDrugBundle:Company')->findByCompanyID($CompanyID);

		if ($company == null) {
			throw $this->createNotFoundException();
		}

		$products = $em->getRepository('VidalDrugBundle:Product')->findByOwner($CompanyID);

		# находим представительства
		$productsRepresented = array();
		for ($i = 0; $i < count($products); $i++) {
			$key = $products[$i]['InfoPageID'];
			if (!empty($key) && !isset($productsRepresented[$key])) {
				$productsRepresented[$key] = $products[$i];
			}
		}

		$params = array(
			'title'               => $this->strip($company['CompanyName']) . ' | Фирмы-производители',
			'company'             => $company,
			'productsRepresented' => $productsRepresented,
			'products'            => $products,
		);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
		}

		return $params;
	}

	/**
	 * Список препаратов по клиннико-фармакологической группе
	 *
	 * @Route("/drugs/cl-ph-group/{description}", name="clphgroup")
	 * @Template("VidalDrugBundle:Vidal:clphgroup.html.twig")
	 */
	public function clphgroupAction($description)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$products = $em->getRepository('VidalDrugBundle:Product')->findByClPhGroup($description);
		$params   = array(
			'products'    => $products,
			'description' => $description,
			'title'       => 'Клинико-фармакологическая группа',
		);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
		}

		return $params;
	}

	/**
	 * Страничка представительства и список препаратов
	 *
	 * @Route("/drugs/company/{InfoPageID}", name="inf_item", requirements={"InfoPageID":"\d+"})
	 * @Route("/poisk_preparatov/inf_{InfoPageID}.{ext}", name="inf_item_old", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/linf_{InfoPageID}.{ext}", name="linf_item_old", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:inf_item.html.twig")
	 */
	public function infItemAction($InfoPageID)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$infoPage = $em->getRepository('VidalDrugBundle:InfoPage')->findByInfoPageID($InfoPageID);

		if (!$infoPage) {
			throw $this->createNotFoundException();
		}

		$picture     = $em->getRepository('VidalDrugBundle:Picture')->findByInfoPageID($InfoPageID);
		$documentIds = $em->getRepository('VidalDrugBundle:Document')->findIdsByInfoPageID($InfoPageID);
		$params      = array(
			'infoPage'   => $infoPage,
			'picture'    => $picture,
			'title'      => $this->strip($infoPage['RusName']) . ' | Представительства фирм',
			'portfolios' => $em->getRepository('VidalDrugBundle:InfoPage')->findPortfolios($InfoPageID),
		);

		if (!empty($documentIds)) {
			$products = $em->getRepository('VidalDrugBundle:Product')->findByDocumentIDs($documentIds);

			if (!empty($products)) {
				$productIds          = $this->getProductIds($products);
				$params['products']  = $products;
				$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
				$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
			}
		}

		return $params;
	}

	/**
	 * Страничка представительств
	 *
	 * @Route("/drugs/companies", name="inf", requirements={"InfoPageID":"\d+"})
	 * @Template("VidalDrugBundle:Vidal:inf.html.twig")
	 */
	public function infAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$q  = $request->query->get('q', null);
		$l  = $request->query->get('l', null);
		$p  = $request->query->get('p', 1);

		//		$companies = $em->getRepository('VidalDrugBundle:InfoPage')->getQuery()->getResult();
		//		$letters   = array();
		//		foreach ($companies as $company) {
		//			$letter = mb_strtoupper(mb_substr($company->getRusName(), 0, 1, 'utf-8'), 'utf-8');
		//			if (!isset($letters[$letter])) {
		//				$letters[$letter] = '';
		//			}
		//		}
		//		var_dump($letters);
		//		exit;

		if ($l) {
			$query = $em->getRepository('VidalDrugBundle:InfoPage')->findByLetter($l);
		}
		elseif ($q) {
			$query = $em->getRepository('VidalDrugBundle:InfoPage')->findByQuery($q);
		}
		else {
			$query = $em->getRepository('VidalDrugBundle:InfoPage')->getQuery();
		}

		$params = array(
			'menu_drugs' => 'inf',
			'title'      => 'Представительства фирм',
			'q'          => $q,
			'l'          => $l,
			'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::COMPANIES_PER_PAGE),
		);

		return $params;
	}

	/**
	 * @Route("/drugs/molecules", name="molecules")
	 * @Template("VidalDrugBundle:Vidal:molecules.html.twig")
	 */
	public function moleculesAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$q  = $request->query->get('q', null);
		$l  = $request->query->get('l', null);
		$p  = $request->query->get('p', 1);

		//		$molecules = $em->getRepository('VidalDrugBundle:Molecule')->getQuery()->getResult();
		//		$letters   = array();
		//		foreach ($molecules as $m) {
		//			$letter = mb_strtoupper(mb_substr($m->getRusName(), 0, 1, 'utf-8'), 'utf-8');
		//			if (!isset($letters[$letter])) {
		//				$letters[$letter] = '';
		//			}
		//		}
		//		var_dump($letters);
		//		exit;

		if ($l) {
			$query = $em->getRepository('VidalDrugBundle:Molecule')->getQueryByLetter($l);
		}
		elseif ($q) {
			$query = $em->getRepository('VidalDrugBundle:Molecule')->getQueryByString($q);
		}
		else {
			$query = $em->getRepository('VidalDrugBundle:Molecule')->getQuery();
		}

		$params = array(
			'menu_drugs' => 'molecule',
			'title'      => 'Активные вещества',
			'q'          => $q,
			'l'          => $l,
			'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::MOLECULES_PER_PAGE),
		);

		return $params;
	}

	/**
	 * Список препаратов по активному веществу: одно-монокомпонентные
	 *
	 * @Route("/drugs/molecule/{MoleculeID}", name="molecule", requirements={"MoleculeID":"\d+"})
	 * @Route("/poisk_preparatov/act_{MoleculeID}.{ext}", name="molecule_old", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:molecule.html.twig")
	 */
	public function moleculeAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$molecule = $em->getRepository('VidalDrugBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		$document = $em->getRepository('VidalDrugBundle:Document')->findByMoleculeID($MoleculeID);

		return array(
			'molecule' => $molecule,
			'document' => $document,
			'title'    => $molecule->getTitle() . ' | Активные вещества',
		);
	}

	/**
	 * Отображение списка препаратов, в состав которых входит активное вещество (Molecule)
	 *
	 * @Route("/drugs/molecule-in/{MoleculeID}", name="molecule_included", requirements={"MoleculeID":"\d+"})
	 * @Route("/poisk_preparatov/lact_{MoleculeID}.{ext}", name="molecule_included_old", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:molecule_included.html.twig")
	 */
	public function moleculeIncludedAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$molecule = $em->getRepository('VidalDrugBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		# все продукты по активному веществу и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByMoleculeID($MoleculeID);

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
		$components = $em->getRepository('VidalDrugBundle:Molecule')->countComponents($productIds);
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
			'companies' => $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y')),
			'infoPages' => $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($productsRaw),
			'title'     => $molecule->getTitle() . ' | Активные вещества в препаратах',
		);
	}

	/**
	 * Страничка рассшифровки МНН аббревиатур
	 *
	 * @Route("drugs/gnp", name="gnp")
	 * @Route("poisk_preparatov/gnp.{ext}", name="gnp_old", defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:gnp.html.twig")
	 */
	public function gnpAction()
	{
		return array(
			'title' => 'Международные наименования - МНН',
		);
	}

	/**
	 * Описание препарата
	 *
	 * @Route("/drugs/{EngName}__{ProductID}.{ext}", name="product", requirements={"ProductID":"\d+", "EngName"=".+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", name="product_old", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalDrugBundle:Vidal:document.html.twig")
	 */
	public function productAction($EngName, $ProductID)
	{
		$em = $this->getDoctrine()->getManager('drug');

		$product = $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID);

		if (!$product
			|| $product->getName() != $EngName
			|| !in_array($product->getMarketStatusID()->getMarketStatusID(), array(2, 5))
		) {
			throw $this->createNotFoundException();
		}

		$params   = array();
		$document = $product->getDocument();

		# условите от Марии, что бады должны иметь Document.ArticleID = 6
		if ($product->getProductTypeCode() == 'BAD' && $document && $document->getArticleID() != 6) {
			$document = null;
		}

		if ($document) {
			$documentId           = $document->getDocumentID();
			$params['document']   = $document;
			$params['infoPages']  = $em->getRepository('VidalDrugBundle:InfoPage')->findByDocumentID($documentId);
			$params['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')->findByDocumentID($documentId);
		}

		$productIds             = array($product->getProductID());
		$params['product']      = $product;
		$params['products']     = array($product);
		$params['owners']       = $em->getRepository('VidalDrugBundle:Company')->findOwnersByProducts($productIds);
		$params['distributors'] = $em->getRepository('VidalDrugBundle:Company')->findDistributorsByProducts($productIds);

		# БАДы выводятся по-другому
		if ($product->isBAD() || ($document && $document->isBAD())) {
			$params['pictures'] = $em->getRepository('VidalDrugBundle:Picture')->findAllByProductIds($productIds);
			$params['title']    = $this->strip($product->getRusName()) . ' | БАДы';

			return $this->render("VidalDrugBundle:Vidal:bad_document.html.twig", $params);
		}
		else {
			$params['pictures'] = $em->getRepository('VidalDrugBundle:Picture')->findAllByProductIds($productIds, date('Y'));
			$params['title']    = $this->strip($product->getRusName()) . ' | Препараты';
		}

		return $params;
	}

	/**
	 * Описание по документу и отображение информации по препаратам или веществу
	 *
	 * @Route("/drugs/{EngName}~{DocumentID}", name="document", requirements={"DocumentID":"\d+"})
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document_old", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}.{ext}", name="document_name_old", defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Vidal:document.html.twig")
	 */
	public function documentAction($EngName, $DocumentID = null)
	{
		$em     = $this->getDoctrine()->getManager('drug');
		$params = array();

		$document = $DocumentID
			? $em->getRepository('VidalDrugBundle:Document')->findById($DocumentID)
			: $em->getRepository('VidalDrugBundle:Document')->findByName($EngName);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		if (!$DocumentID) {
			$DocumentID = $document->getDocumentID();
		}

		$params['title']      = $this->strip($document->getRusName()) . ' | Препараты';
		$params['documentId'] = $document->getDocumentID();
		$articleId            = $document->getArticleID();
		$molecules            = $em->getRepository('VidalDrugBundle:Molecule')->findByDocumentID($DocumentID);

		$products = $articleId == 1
			? $em->getRepository('VidalDrugBundle:Product')->findByMolecules($molecules)
			: $em->getRepository('VidalDrugBundle:Product')->findByDocumentID($DocumentID);

		if (!empty($products)) {
			$productIds             = $this->getProductIds($products);
			$params['atcCodes']     = $em->getRepository('VidalDrugBundle:ATC')->findByProducts($productIds);
			$params['owners']       = $em->getRepository('VidalDrugBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalDrugBundle:Company')->findDistributorsByProducts($productIds);
			$params['pictures']     = $em->getRepository('VidalDrugBundle:Picture')->findAllByProductIds($productIds, date('Y'));
		}
		else {
			$params['atcCodes'] = $em->getRepository('VidalDrugBundle:ATC')->findByDocumentID($DocumentID);
			$params['pictures'] = array();
		}

		$params['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')->findByDocumentID($DocumentID);
		$params['articleId']  = $articleId;
		$params['document']   = $document;
		$params['molecules']  = $molecules;
		$params['products']   = $products;
		$params['infoPages']  = $em->getRepository('VidalDrugBundle:InfoPage')->findByDocumentID($DocumentID);

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

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}
}
