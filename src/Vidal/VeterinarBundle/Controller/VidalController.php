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
	 * @Route("veterinar", name="veterinar")
	 * @Template("VidalVeterinarBundle:Vidal:veterinar.html.twig")
	 */
	public function veterinarAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('veterinar');
		$t  = $request->query->get('t', 'p'); // тип препараты=p / производители=c / представительства=r
		$p  = $request->query->get('p', 1); // номер страницы
		$l  = $request->query->get('l', null); // буква
		$q  = $request->query->get('q', null); // текстовый запрос

		$params = array(
			't'              => $t,
			'p'              => $p,
			'l'              => $l,
			'q'              => $q,
			'title'          => 'Видаль-Ветеринар',
			'menu_veterinar' => 'veterinar',
		);

		# если выбрали препараты
		if ($t == 'p') {
			if ($l != null) {
				$paginator  = $this->get('knp_paginator');
				$pagination = $paginator->paginate(
					$em->getRepository('VidalVeterinarBundle:Product')->getQueryByLetter($l),
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
					$params['indications'] = $em->getRepository('VidalVeterinarBundle:Document')->findIndicationsByProductIds($productIds);
					$params['companies']   = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
					$params['pictures']    = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
				}
			}
		}

		return $params;
	}

	/**
	 * Клинико-фармакологический указатель ветеринарной базы
	 *
	 * @Route("veterinar/kfu/{name}", name = "v_kfu")
	 * @Template("VidalVeterinarBundle:Vidal:kfu.html.twig")
	 */
	public function kfuAction($name = null)
	{
		$params = array(
			'menu_veterinar' => 'kfu',
		);

		return $params;
	}

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
	 * Страничка представительства и список препаратов
	 * @Route("veterinar/inf_{InfoPageID}.{ext}", name="v_inf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
	 * @Route("veterinar/linf_{InfoPageID}.{ext}", name="v_linf", requirements={"InfoPageID":"\d+"}, defaults={"ext"="htm"})
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
	 * @Route("veterinar/act_{MoleculeID}.{ext}", name="v_molecule", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
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
	 * @Route("veterinar/lact_{MoleculeID}.{ext}", name="v_molecule_included", requirements={"MoleculeID":"\d+"}, defaults={"ext"="htm"})
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
	 * @Route("veterinar/gnp.{ext}", name="v_gnp", defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:gnp.html.twig")
	 */
	public function gnpAction()
	{
		return array();
	}

	/**
	 * Описание по документу и отображение информации по препаратам или веществу
	 * @Route("veterinar/opisanie/{name}.{ext}", name="v_document", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
	 *
	 * @Template("VidalVeterinarBundle:Vidal:document.html.twig")
	 */
	public function documentAction($name)
	{
		$em         = $this->getDoctrine()->getManager('veterinar');
		$params     = array();
		$DocumentID = intval(substr($name, strrpos($name, '_') + 1));

		$document = $em->getRepository('VidalVeterinarBundle:Document')->findById($DocumentID);

		if (!$document) {
			throw $this->createNotFoundException();
		}

		$params['documentId'] = $document->getDocumentID();
		$molecules            = $em->getRepository('VidalVeterinarBundle:Molecule')->findByDocumentID($DocumentID);

		$products = $em->getRepository('VidalVeterinarBundle:Product')->findByDocumentID($DocumentID);

		if (!empty($products)) {
			$productIds             = $this->getProductIds($products);
			$params['owners']       = $em->getRepository('VidalVeterinarBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalVeterinarBundle:Company')->findDistributorsByProducts($productIds);
			$params['pictures']     = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
		}
		else {
			$params['pictures'] = array();
		}

		$params['document']  = $document;
		$params['molecules'] = $molecules;
		$params['products']  = $products;
		$params['infoPages'] = $em->getRepository('VidalVeterinarBundle:InfoPage')->findByDocumentID($DocumentID);

		return $params;
	}

	/**
	 * Описание препарата
	 * @Route("veterinar/{EngName}~{ProductID}.{ext}", name="v_product", requirements={"ProductID":"\d+"}, defaults={"ext"="htm"})
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

		$productIds             = array($product['ProductID']);
		$params['product']      = $product;
		$params['products']     = array($product);
		$params['molecules']    = $molecules;
		$params['owners']       = $em->getRepository('VidalVeterinarBundle:Company')->findOwnersByProducts($productIds);
		$params['distributors'] = $em->getRepository('VidalVeterinarBundle:Company')->findDistributorsByProducts($productIds);
		$params['pictures']     = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);

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
