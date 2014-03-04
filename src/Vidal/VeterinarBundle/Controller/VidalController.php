<?php
namespace Vidal\VeterinarBundle\Controller;

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
	 * @Route("veterinar/fir_{CompanyID}.{ext}", name="v_company", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("veterinar/lfir_{CompanyID}.{ext}", name="v_company_products", requirements={"CompanyID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:company.html.twig")
	 */
	public function companyAction($CompanyID)
	{
		$em      = $this->getDoctrine()->getManager('veterinar');
		$company = $em->getRepository('VidalVeterinarBundle:Company')->findByCompanyID($CompanyID);

		if ($company == null) {
			throw $this->createNotFoundException();
		}

		$productsRaw = $em->getRepository('VidalVeterinarBundle:Product')->findByOwner($CompanyID);

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
	 * @Route("veterinar/poisk_preparatov/lat_{ATCCode}.{ext}", name="v_atc", options={"expose":true}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:atc.html.twig")
	 */
	public function atcAction($ATCCode)
	{
		$em  = $this->getDoctrine()->getManager('veterinar');
		$atc = $em->getRepository('VidalVeterinarBundle:ATC')->findOneByATCCode($ATCCode);

		if (!$atc) {
			throw $this->createNotFoundException();
		}

		# все продукты по ATC-коду и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalVeterinarBundle:Product')->findByATCCode($ATCCode);
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
			'companies' => $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds)
		);
	}

	/**
	 * Список препаратов по клиннико-фармакологической группе
	 * @Route("poisk_preparatov/cl-ph-group/{description}", name="clphgroup")
	 * @Template("VidalVeterinarBundle:Vidal:clphgroup.html.twig")
	 */
	public function clphgroupAction($description)
	{
		$em       = $this->getDoctrine()->getManager('veterinar');
		$products = $em->getRepository('VidalVeterinarBundle:Product')->findByClPhGroup($description);
		$params   = array('products' => $products, 'description' => $description);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список препаратов по фармако-терапевтической группе
	 * @Route("poisk_preparatov/ph-th-group/{id}", name="phthgroup", defaults={"id":"\d+"})
	 * @Template("VidalVeterinarBundle:Vidal:phthgroup.html.twig")
	 */
	public function phthgroupAction($id)
	{
		$em        = $this->getDoctrine()->getManager('veterinar');
		$phthgroup = $em->getRepository('VidalVeterinarBundle:PhThGroups')->findById($id);

		if ($phthgroup === null) {
			throw $this->createNotFoundException();
		}

		$products = $em->getRepository('VidalVeterinarBundle:Product')->findByPhThGroup($id);
		$params   = array('phthgroup' => $phthgroup, 'products' => $products);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список препаратов и активных веществ по показанию (Nozology)
	 * @Route("poisk_preparatov/lno_{NozologyCode}", name="nozology_code")
	 * @Template("VidalVeterinarBundle:Vidal:nozology_code.html.twig")
	 */
	public function nozologyCodeAction($NozologyCode)
	{
		$em = $this->getDoctrine()->getManager('veterinar');

		if ($pos = strpos($NozologyCode, '.htm')) {
			$NozologyCode = substr($NozologyCode, $pos, 4);
		}

		$nozology = $em->getRepository('VidalVeterinarBundle:Nozology')->findByCode($NozologyCode);

		if ($nozology === null) {
			throw $this->createNotFoundException();
		}

		$documents = $em->getRepository('VidalVeterinarBundle:Document')->findByNozologyCode($NozologyCode);
		$params    = array('nozology' => $nozology);

		if (!empty($documents)) {
			$params['molecules'] = $em->getRepository('VidalVeterinarBundle:Molecule')->findByDocuments1($documents);
			$products1           = $em->getRepository('VidalVeterinarBundle:Product')->findByDocuments25($documents);
			$products2           = $em->getRepository('VidalVeterinarBundle:Product')->findByDocuments4($documents);
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
			$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Страничка представительства и список препаратов
	 * @Route("poisk_preparatov/inf_{InfoPageID}.{ext}", name="inf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("poisk_preparatov/linf_{InfoPageID}.{ext}", name="linf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:inf.html.twig")
	 */
	public function infAction($InfoPageID)
	{
		$em       = $this->getDoctrine()->getManager('veterinar');
		$infoPage = $em->getRepository('VidalVeterinarBundle:InfoPage')->findByInfoPageID($InfoPageID);

		if (!$infoPage) {
			throw $this->createNotFoundException();
		}

		$picture     = $em->getRepository('VidalVeterinarBundle:Picture')->findByInfoPageID($InfoPageID);
		$params      = array('infoPage' => $infoPage, 'picture' => $picture);
		$documentIds = $em->getRepository('VidalVeterinarBundle:Document')->findIdsByInfoPageID($InfoPageID);

		if (!empty($documentIds)) {
			$products = $em->getRepository('VidalVeterinarBundle:Product')->findByDocumentIDs($documentIds);

			if (!empty($products)) {
				$productIds          = $this->getProductIds($products);
				$params['products']  = $products;
				$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
			}
		}

		return $params;
	}

	/**
	 * Список препаратов по активному веществу: одно-монокомпонентные
	 * @Route("poisk_preparatov/act_{MoleculeID}.{ext}", name="molecule", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:molecule.html.twig")
	 */
	public function moleculeAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager('veterinar');
		$molecule = $em->getRepository('VidalVeterinarBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		$document = $em->getRepository('VidalVeterinarBundle:Document')->findByMoleculeID($MoleculeID);

		return array(
			'molecule' => $molecule,
			'document' => $document,
		);
	}

	/**
	 * Отображение списка препаратов, в состав которых входит активное вещество (Molecule)
	 * @Route("poisk_preparatov/lact_{MoleculeID}.{ext}", name="molecule_included", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:molecule_included.html.twig")
	 */
	public function moleculeIncludedAction($MoleculeID)
	{
		$em       = $this->getDoctrine()->getManager('veterinar');
		$molecule = $em->getRepository('VidalVeterinarBundle:Molecule')->findByMoleculeID($MoleculeID);

		if (!$molecule) {
			throw $this->createNotFoundException();
		}

		# все продукты по активному веществу и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalVeterinarBundle:Product')->findByMoleculeID($MoleculeID);

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
		$components = $em->getRepository('VidalVeterinarBundle:Molecule')->countComponents($productIds);
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
			'companies' => $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds),
		);
	}

	/**
	 * Страничка рассшифровки МНН аббревиатур
	 * @Route("poisk_preparatov/gnp.{ext}", name="gnp", defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:gnp.html.twig")
	 */
	public function gnpAction()
	{
		return array();
	}

	/**
	 * Описание препарата
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", name="product", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:document.html.twig")
	 */
	public function productAction($EngName, $ProductID)
	{
		$em     = $this->getDoctrine()->getManager('veterinar');
		$params = array();

		$product = $em->getRepository('VidalVeterinarBundle:Product')->findByProductID($ProductID);

		if (!$product) {
			throw $this->createNotFoundException();
		}

		$document  = $em->getRepository('VidalVeterinarBundle:Document')->findByProductDocument($ProductID);
		$molecules = $em->getRepository('VidalVeterinarBundle:Molecule')->findByProductID($ProductID);

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
			$params['infoPages'] = $em->getRepository('VidalVeterinarBundle:InfoPage')->findByDocumentID($document->getDocumentID());
		}
		else {
			# если связи ProductDocument не найдено, то это описание конкретного вещества (Molecule)
			$molecule = $em->getRepository('VidalVeterinarBundle:Molecule')->findOneByProductID($ProductID);

			if ($molecule) {
				$document = $em->getRepository('VidalVeterinarBundle:Document')->findByMoleculeID($molecule['MoleculeID']);

				if (!$document) {
					throw $this->createNotFoundException();
				}

				$params['document']  = $document;
				$params['molecule']  = $molecule;
				$params['articleId'] = $document->getArticleId();
				$params['infoPages'] = $em->getRepository('VidalVeterinarBundle:InfoPage')->findByDocumentID($document->getDocumentID());
			}
		}

		if ($document) {
			$params['nozologies'] = $em->getRepository('VidalVeterinarBundle:Nozology')->findByDocumentID($document->getDocumentID());
		}

		$productIds             = array($product['ProductID']);
		$params['product']      = $product;
		$params['products']     = array($product);
		$params['molecules']    = $molecules;
		$params['atcCodes']     = $em->getRepository('VidalVeterinarBundle:ATC')->findByProducts($productIds);
		$params['owners']       = $em->getRepository('VidalVeterinarBundle:Company')->findOwnersByProducts($productIds);
		$params['distributors'] = $em->getRepository('VidalVeterinarBundle:Company')->findDistributorsByProducts($productIds);
		$params['pictures']     = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		$params['phthgroups']   = $em->getRepository('VidalVeterinarBundle:PhThGroups')->findByProductId($ProductID);

		return $params;
	}

	/**
	 * Описание по документу и отображение информации по препаратам или веществу
	 * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", name="document", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("/poisk_preparatov/{EngName}.{ext}", name="document_name", defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:document.html.twig")
	 */
	public function documentAction($EngName, $DocumentID = null)
	{
		$em     = $this->getDoctrine()->getManager('veterinar');
		$params = array();

		$document = $DocumentID
			? $em->getRepository('VidalVeterinarBundle:Document')->findById($DocumentID)
			: $em->getRepository('VidalVeterinarBundle:Document')->findByName($EngName);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		if (!$DocumentID) {
			$DocumentID = $document->getDocumentID();
		}

		$params['documentId'] = $document->getDocumentID();
		$articleId            = $document->getArticleID();
		$molecules            = $em->getRepository('VidalVeterinarBundle:Molecule')->findByDocumentID($DocumentID);

		$products = $articleId == 1
			? $em->getRepository('VidalVeterinarBundle:Product')->findByMolecules($molecules)
			: $em->getRepository('VidalVeterinarBundle:Product')->findByDocumentID($DocumentID);

		if (!empty($products)) {
			$productIds             = $this->getProductIds($products);
			$params['atcCodes']     = $em->getRepository('VidalVeterinarBundle:ATC')->findByProducts($productIds);
			$params['owners']       = $em->getRepository('VidalVeterinarBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalVeterinarBundle:Company')->findDistributorsByProducts($productIds);
			$params['pictures']     = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		}
		else {
			$params['atcCodes'] = $em->getRepository('VidalVeterinarBundle:ATC')->findByDocumentID($DocumentID);
			$params['pictures'] = array();
		}

		$params['nozologies'] = $em->getRepository('VidalVeterinarBundle:Nozology')->findByDocumentID($DocumentID);
		$params['articleId']  = $articleId;
		$params['document']   = $document;
		$params['molecules']  = $molecules;
		$params['products']   = $products;
		$params['infoPages']  = $em->getRepository('VidalVeterinarBundle:InfoPage')->findByDocumentID($DocumentID);

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
