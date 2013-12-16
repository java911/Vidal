<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VidalController extends Controller
{
	/**
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
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc", defaults={"ext"="htm"})
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
			'companies' => $productCompanies,
			'pictures'  => $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds)
		);
	}

	/**
	 * @Route("poisk_preparatov/nosology/{NosologyCode}", name="nosology_code")
	 * @Template("VidalMainBundle:Vidal:nosology_code.html.twig")
	 */
	public function nosologyAction($NosologyCode)
	{

//		$documents  = $em->getRepository('VidalMainBundle:Document')->findByNozologies($nozologies);
//		$molecules  = $em->getRepository('VidalMainBundle:Molecule')->findByDocuments1($documents);
//		$products1  = $em->getRepository('VidalMainBundle:Product')->findByDocuments25($documents);
//		$products2  = $em->getRepository('VidalMainBundle:Product')->findByDocuments4($documents);
//
//		# надо слить продукты, исключая повторения и отсортировать по названию
//		$products = array();
//		foreach ($products1 as $id => $product) {
//			$products[] = $product;
//		}
//		foreach ($products2 as $id => $product) {
//			if (!isset($products1[$id])) {
//				$products[] = $product;
//			}
//		}
//		usort($products, function ($a, $b) {
//			return strcmp($a['RusName'], $b['RusName']);
//		});
//
//		$noz = array();
//		foreach ($nozologies as $nozology) {
//			$key = $nozology['code'];
//			$noz[$key] = array('name' => $nozology['name']);
//		}
//
//		foreach ($documents as $document) {
//			$key = $document['NozologyCode'];
//			$noz[$key]['documents'][] = $document;
//		}
//
//		foreach ($noz as $code => &$nozology) {
//			$products1 = $em->getRepository('VidalMainBundle:Product')->findByDocuments25($nozology['documents']);
//			$products2 = $em->getRepository('VidalMainBundle:Product')->findByDocuments4($nozology['documents']);
//		}
//
//		# сгруппировать продукты по показаниям
//		foreach ($products as $product) {
//			foreach ($noz) {
//
//			}
//			isset($documentsGrouped[$key])
//				? $documentsGrouped[$key][] = $product
//				: $documentsGrouped[$key] = array($product);
//		}
//
//		# сгруппировать молекулы по документом
//		foreach ($molecules as $molecule) {
//			$key = $molecule['DocumentID'];
//			isset($documentsGrouped[$key]['molecules'])
//				? $documentsGrouped[$key]['molecules'][] = $molecule
//				: $documentsGrouped[$key]['molecules'] = array($molecule);
//		}
	}

	/**
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

		$productsRaw = $em->getRepository('VidalMainBundle:Product')->findByInfoPageID($InfoPageID);
		$products    = array();

		# надо отсеить дубли
		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];
			if (!isset($products[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		$picture = $em->getRepository('VidalMainBundle:Picture')->findByInfoPageID($InfoPageID);

		return array(
			'infoPage' => $infoPage,
			'products' => $products,
			'picture'  => $picture,
		);
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
			'molecule'  => $molecule,
			'products1' => $products1,
			'products2' => $products2,
			'companies' => $productCompanies,
			'pictures'  => $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds),
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

	/** Отсортировать препараты по имени */
	private function sortProducts($a, $b)
	{
		return strcasecmp($a['RusName'], $b['RusName']);
	}

	/**
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
			$productIds = array();
			foreach ($products as $product) {
				$productIds[] = $product['ProductID'];
			}

			$params['atcCodes']     = $em->getRepository('VidalMainBundle:ATC')->findByProducts($productIds);
			$params['owners']       = $em->getRepository('VidalMainBundle:Company')->findOwnersByProducts($productIds);
			$params['distributors'] = $em->getRepository('VidalMainBundle:Company')->findDistributorsByProducts($productIds);
			$params['pictures']     = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
		}
		else {
			$params['atcCodes'] = $em->getRepository('VidalMainBundle:ATC')->findByDocumentID($DocumentID);
			$params['pictures'] = array();
		}

		$params['articleId'] = $articleId;
		$params['document']  = $document;
		$params['molecules'] = $molecules;
		$params['products']  = $products;
		$params['infoPages'] = $em->getRepository('VidalMainBundle:InfoPage')->findByDocumentID($DocumentID);

		return $params;
	}

	/**
	 * @Route("/test/email", name="test_email")
	 */
	public function testEmail()
	{
		$message = \Swift_Message::newInstance()
			->setSubject('Сообщение из формы обратной связи')
			->setContentType('text/html')
			->setCharset('utf-8')
			->setFrom('noreply@mailbot.evrika.ru', 'Testing')
			->setTo('7binary@gmail.com')
			->setBody('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>Its a simple message</body></html>');

		$this->get('mailer')->send($message);

		return new Response();
	}
}
