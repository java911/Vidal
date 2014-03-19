<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DrugsController extends Controller
{
	/**
	 * Препараты по коду АТХ
	 *
	 * @Route("drugs/atc/{ATCCode}", name="atc_item", options={"expose":true})
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc_item_old", defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Drugs:atc_item.html.twig")
	 */
	public function atcItemAction($ATCCode)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$atc = $em->getRepository('VidalDrugBundle:ATC')->findOneByATCCode($ATCCode);

		if (!$atc) {
			throw $this->createNotFoundException();
		}

		# все продукты по ATC-коду и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByATCCode($ATCCode);
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
			'companies' => $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds)
		);
	}

	/**
	 * Дерево АТХ
	 *
	 * @Route("drugs", name="drugs")
	 * @Route("drugs/atc", name="atc")
	 * @Template("VidalDrugBundle:Drugs:atc.html.twig")
	 */
	public function atcAction()
	{
		$params = array(
			'menu'       => 'drugs',
			'menu_drugs' => 'atc',
		);

		return $params;
	}

	/**
	 * Препараты по КФУ
	 *
	 * @Route("drugs/kfu/{url}", name="kfu_item")
	 * @Template("VidalDrugBundle:Drugs:kfu_item.html.twig")
	 */
	public function kfuItemAction($url)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$kfu = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findOneByUrl($url);

		var_dump($kfu);
		exit;

		$params = array(
			'menu'       => 'drugs',
			'menu_drugs' => 'kfu',
		);

		return $params;
	}

	/**
	 * Дерево КФУ
	 *
	 * @Route("drugs/kfu", name="kfu")
	 * @Template("VidalDrugBundle:Drugs:kfu.html.twig")
	 */
	public function kfuAction()
	{
		$params = array(
			'menu'       => 'drugs',
			'menu_drugs' => 'kfu',
		);

		return $params;
	}

	/**
	 * Функция генерации дерева с кодами КФУ
	 *
	 * @Route("drugs/kfu-generator", name="kfu_generator")
	 * @Template("VidalDrugBundle:Drugs:kfu_generator.html.twig")
	 */
	public function kfuGeneratorAction()
	{
		$em    = $this->getDoctrine()->getManager('drug');
		$repo  = $em->getRepository('VidalDrugBundle:ClinicoPhPointers');
		$codes = $repo->findForTree();

		# надо сгруппировать по родителю (запихпуть в list родителя дочерние)
		for ($i = 14; $i > 0; $i = $i - 3) {
			foreach ($codes as $codeValue => $code) {
				if (strlen($codeValue) == $i) {
					$key = substr($codeValue, 0, -3);
					if (isset($codes[$key]) && strlen($codeValue) > strlen($key)) {
						$codes[$key]['list'][$codeValue] = $code;
					}
				}
			}
		}

		$grouped = array();

		foreach ($codes as $codeValue => $code) {
			if (strlen($codeValue) == 2) {
				$grouped[] = $code;
			}
		}

		return array('codes' => $grouped);
	}
}
