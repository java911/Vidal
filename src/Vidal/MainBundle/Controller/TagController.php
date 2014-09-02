<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
	const NEWS_PER_PAGE  = 12;
	const PHARM_PER_PAGE = 4;

	/**
	 * @Route("/tag/news/{id}", name="tag_news")
	 * @Template("VidalMainBundle:Tag:tag_news.html.twig")
	 */
	public function tagNewsAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Новости',
		);

		$query                = $em->getRepository('VidalDrugBundle:Publication')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/articles/{id}", name="tag_articles")
	 * @Template("VidalMainBundle:Tag:tag_articles.html.twig")
	 */
	public function tagArticlesAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Медицинская энциклопедия',
		);

		$query                = $em->getRepository('VidalDrugBundle:Article')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/arts/{id}", name="tag_arts")
	 * @Template("VidalMainBundle:Tag:tag_arts.html.twig")
	 */
	public function tagArtsAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Статьи специалистам',
		);

		$query                = $em->getRepository('VidalDrugBundle:Art')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/pharm-articles/{id}", name="tag_pharm_articles")
	 * @Template("VidalMainBundle:Tag:tag_pharm_articles.html.twig")
	 */
	public function tagPharmArticlesAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Статьи специалистам',
		);

		$query                = $em->getRepository('VidalDrugBundle:PharmArticle')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::PHARM_PER_PAGE);

		return $params;
	}

	/**
	 * @Template("VidalMainBundle:Tag:tags.html.twig")
	 */
	public function tagsAction($object)
	{
		$tags        = array();
		$infoPageIds = array();
		$em          = $this->getDoctrine()->getManager('drug');

		# теги
		foreach ($object->getTags() as $tag) {
			$key = $tag->getText();
			# проверка, что это представительство
			if (preg_match('/[A-Z]/', $key) || preg_match('/[А-Я]/u', $key)) {
				$infoPage = $em->getRepository('VidalDrugBundle:InfoPage')->findByCompanyName($key);
				if ($infoPage) {
					$infoPageIds[] = $infoPage->getInfoPageID();
					$tags[$key]    = $infoPage;
					break;
				}
			}

			$hasPublication = false;
			foreach ($tag->getPublications() as $publication) {
				if ($publication->getEnabled()) {
					$hasPublication = true;
					break;
				}
			}
			if (!isset($tags[$key]) && $hasPublication) {
				$tags[$key] = $tag;
			}
		}
		uksort($tags, array($this, 'casecmp'));

		# активные вещества
		$tagsMolecules = array();

		foreach ($object->getMolecules() as $molecule) {
			$rusName = $molecule->getRusName();
			$key     = empty($rusName) ? $molecule->getLatName() : $rusName;
			if (!isset($tagsMolecules[$key])) {
				$tagsMolecules[$key] = $molecule;
			}
		}

		if (count($tagsMolecules)) {
			uksort($tagsMolecules, array($this, 'casecmp'));
			$tags = array_merge($tags, $tagsMolecules);
		}

		#АТХ
		$tagsAtc = array();

		foreach ($object->getAtcCodes() as $atc) {
			$key = $atc->getATCCode() . ' - ' . $atc->getRusName();
			if (!isset($tagsAtc[$key])) {
				$tagsAtc[$key] = $atc;
			}
		}

		if (count($tagsAtc)) {
			uksort($tagsAtc, array($this, 'casecmp'));
			$tags = array_merge($tags, $tagsAtc);
		}

		# Нозология МКБ-10
		$tagsNozologies = array();

		foreach ($object->getNozologies() as $nozology) {
			$key = $nozology->getCode() . ' - ' . $nozology->getName();
			if (!isset($tagsNozologies[$key])) {
				$tagsNozologies[$key] = $nozology;
			}
		}

		if (count($tagsNozologies)) {
			uksort($tagsNozologies, array($this, 'casecmp'));
			$tags = array_merge($tags, $tagsNozologies);
		}

		# Представительства
		$tagsInfopages = array();

		foreach ($object->getInfoPages() as $ip) {
			$key = $ip->getRusName();
			if (!in_array($ip->getInfoPageID(), $infoPageIds)) {
				$tagsInfopages[$key] = $ip;
			}
		}

		if (count($tagsInfopages)) {
			uksort($tagsInfopages, array($this, 'casecmp'));
			$tags = array_merge($tags, $tagsInfopages);
		}

		$products    = array();
		$productsRaw = $object->getProducts();

		if (!empty($productsRaw)) {
			foreach ($productsRaw as $product) {
				if ($product->isValid()) {
					$key = $this->strip($product->getRusName());
					isset($products[$key])
						? $products[$key][] = $product
						: $products[$key] = array($product);
				}
			}
		}

		ksort($products);

		return array(
			'tags'          => $tags,
			'productGroups' => $products,
		);
	}

	private function strip($string)
	{
		$pat = array(' /<sup>(.*?)<\/sup >/i', ' /<sub>(.*?)<\/sub >/i', ' /&amp;/');
		$rep = array('', '', ' & ');

		return preg_replace($pat, $rep, $string);
	}

	private function casecmp($a, $b)
	{
		$a = mb_strtolower($a, 'utf-8');
		$b = mb_strtolower($b, 'utf-8');

		return $a == $b ? 0 : ($a > $b ? 1 : -1);
	}
}
