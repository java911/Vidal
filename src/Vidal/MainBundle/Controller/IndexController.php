<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vidal\MainBundle\Entity\MapRegion;
use Vidal\MainBundle\Entity\MapCoord;
use Lsw\SecureControllerBundle\Annotation\Secure;

class IndexController extends Controller
{
	const PUBLICATIONS_SHOW = 5;
	const PUBLICATIONS_LOAD = 4;
	const ARTICLES_SHOW     = 4;
	const ARTICLES_LOAD     = 4;

	/**
	 * @Route("/", name="index")
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');

		$params = array(
			'indexPage'    => true,
			'seotitle'     => 'Справочник лекарственных препаратов Видаль. Описание лекарственных средств',
			'publications' => $em->getRepository('VidalDrugBundle:Publication')->findLast(self::PUBLICATIONS_SHOW),
			'articles'     => $em->getRepository('VidalDrugBundle:Article')->findLast(self::ARTICLES_SHOW),
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка еще нескольких статей на главную
	 * @Route("/ajax-articles/{from}", name="ajax_articles", options={"expose":true})
	 */
	public function ajaxArticlesAction($from)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$articles = $em->getRepository('VidalDrugBundle:Article')->findFrom($from, self::ARTICLES_LOAD);
		$html     = $this->renderView('VidalMainBundle:Article:ajax_articles.html.twig', array('articles' => $articles));

		return new JsonResponse($html);
	}

	/**
	 * [AJAX] Подгрузка еще нескольких новостей на главную
	 * @Route("/ajax-news/{from}", name="ajax_news", options={"expose":true})
	 */
	public function ajaxNewsAction($from)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$news = $em->getRepository('VidalDrugBundle:Publication')->findFrom($from, self::PUBLICATIONS_LOAD);
		$html = $this->renderView('VidalMainBundle:Article:ajax_news.html.twig', array('news' => $news));

		return new JsonResponse($html);
	}

	/**
	 * @Route("/otvety_specialistov", name="qa")
	 * @Template()
	 */
	public function qaAction()
	{
		return array(
			'title'           => 'Вопрос-ответ',
			'menu_left'       => 'qa',
			'questionAnswers' => $this->getDoctrine()->getRepository('VidalMainBundle:QuestionAnswer')->findAll(),
		);
	}

	/** @Route("/Vidal/vidal-russia/Novosti-pharmatsevticheskih-kompanii/") */
	public function r10()
	{
		return $this->redirect($this->generateUrl('pharm_news'), 301);
	}

	/**
	 * @Route("/Vidal/vidal-russia/{url}", name="vidal_russia_item", requirements={"url"=".+"})
	 */
	public function r11($url)
	{
		$url = trim($url, '/');

		return $this->redirect($this->generateUrl('about', array('url' => $url)), 301);
	}

	/**
	 * О компании
	 * @Route("/services", name="services")
	 * @Route("/Vidal/vidal-russia/", name="vidal_russia")
	 *
	 * @Template
	 */
	public function servicesAction()
	{
		$params = array(
			'title'     => 'Наши услуги',
			'menu_left' => 'services',
			'items'     => $this->getDoctrine()->getRepository('VidalMainBundle:About')->findServices(),
		);

		return $params;
	}

	/**
	 * О компании
	 * @Route("/services/{url}", name="services_item")
	 *
	 * @Template()
	 */
	public function servicesItemAction($url)
	{
		$about = $this->getDoctrine()->getRepository('VidalMainBundle:About')->findOneByUrl($url);

		if (empty($about)) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'title'     => $about . ' | Наши услуги',
			'menu_left' => 'services',
			'about'     => $about,
		);

		return $params;
	}

	/**
	 * О компании
	 * @Route("/about/{url}", name="about_item")
	 *
	 * @Template()
	 */
	public function aboutItemAction($url)
	{
		$about = $this->getDoctrine()->getRepository('VidalMainBundle:About')->findOneByUrl($url);

		if (empty($about)) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'title'     => $about . ' | О компании',
			'menu_left' => 'about',
			'about'     => $about,
		);

		return $params;
	}

	/**
	 * О компании
	 * @Route("/about", name="about")
	 *
	 * @Template()
	 */
	public function aboutAction()
	{
		$em = $this->getDoctrine()->getManager();

		$params = array(
			'title'     => 'О компании',
			'menu_left' => 'about',
			'items'     => $this->getDoctrine()->getRepository('VidalMainBundle:About')->findAbout()
		);

		return $params;
	}

	/**
	 * Школа здоровья
	 *
	 * @Route("/shkola_zdorovya/")
	 * @Route("/shkola_zdorovya", name="shkola")
	 * @Template
	 */
	public function shkolaAction()
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArticleRubrique')->findOneByRubrique('shkola-zdorovya');

		$params = array(
			'title'     => 'Школа здоровья',
			'menu_left' => 'shkola',
			'rubrique'  => $rubrique,
			'articles'  => $em->getRepository('VidalDrugBundle:Article')->findByRubriqueId($rubrique->getId()),
		);

		return $params;
	}

	/**
	 * Школа здоровья - статья
	 *
	 * @Route("/shkola_zdorovya/{link}.{ext}", name="shkola_article", defaults={"ext":null})
	 * @Template
	 */
	public function shkolaArticleAction($link, $ext)
	{
		if (!empty($ext)) {
			return $this->redirect($this->generateUrl('shkola_article', array('link' => $link)), 301);
		}

		$em      = $this->getDoctrine()->getManager('drug');
		$article = $em->getRepository('VidalDrugBundle:Article')->findOneByLink($link);

		if (empty($article)) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'title'     => $article->getTitle() . ' | Школа здоровья',
			'menu_left' => 'shkola',
			'article'   => $article,
		);

		return $params;
	}

	/** @Route("/Vidal/partneram/podpisnaya-kompaniya-SV/", name="r1") */
	public function r1()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'spravochnik-vidal')), 301);
	}

	/** @Route("/Vidal/partneram/marketing-Vidal-Specialist/", name="r2") */
	public function r2()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vidal-specialist')), 301);
	}

	/** @Route("/Vidal/partneram/email-mailing/", name="r3") */
	public function r3()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'email-mailing')), 301);
	}

	/** @Route("/Vidal/partneram/basi-dannih-vrachi-sng/", name="r4") */
	public function r4()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vrachi-sng')), 301);
	}

	/** @Route("/Vrachi-Rossii/", name="r5") */
	public function r5()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vrachi-rossii')), 301);
	}

	/** @Route("/Vidal/partneram/Vidal-Vizit/", name="r6") */
	public function r6()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vidal-vizit')), 301);
	}

	/** @Route("/Vidal/partneram/Vidal-Vizit/", name="r7") */
	public function r7()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'cd-versiya')), 301);
	}

	/** @Route("/Vidal/partneram/Kontakti-kommercheskii-otdel/", name="r8") */
	public function r8()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'kommercheskii-otdel')), 301);
	}

	/** @Route("/Vidal/partneram/Krames-obucheniye-patients/", name="r9") */
	public function r9()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'obucheniye')), 301);
	}

	/**
	 * @Route("/vracham/expert/", name="vracham_expert")
	 * @Secure(roles="ROLE_DOCTOR")
	 * @Template
	 */
	public function vrachamExpertAction()
	{
		return array(
			'title' => 'Видаль-Эксперт',
			'menu'  => 'vracham',
		);
	}

	/**
	 * @Route("/module/{moduleId}", name="module")
	 *
	 * @Template("VidalMainBundle:Index:module.html.twig")
	 */
	public function moduleAction($moduleId)
	{
		$em     = $this->getDoctrine()->getManager();
		$module = $em->getRepository('VidalMainBundle:Module')->findOneById($moduleId);

		return array('module' => $module);
	}

	/**
	 * @Route("/pharmacies-map/{id}", name="pharmacies_map", defaults = { "id" = 87 }, options={"expose"=true})
	 * @Template("VidalMainBundle:Index:map.html.twig")
	 */
	public function pharmaciesMapAction($id = 87)
	{
		$cities     = $this->getDoctrine()->getRepository('VidalMainBundle:MapRegion')->findAll();
		$thisCities = $this->getDoctrine()->getRepository('VidalMainBundle:MapRegion')->findOneById($id);

		return array('menu' => 'pharmacies_map', 'cities' => $cities, 'thisCity' => $thisCities);
	}

	/**
	 * @Route("/pharmacies-map-ajax/{cityId}", name="pharmacies_map_ajax", options={"expose"=true})
	 * @Template("VidalMainBundle:Index:map_ajax.json.twig")
	 */
	public function ajaxmapAction($cityId)
	{

		$region = $this->getDoctrine()->getRepository('VidalMainBundle:MapRegion')->findOneById($cityId);
		$coords = $this->getDoctrine()->getRepository('VidalMainBundle:MapCoord')->findByRegion($region);

		return array('coords' => $coords);
	}

	/**
	 * @Route("/getMapHintContent/{id}", name="getMapHintContent", options={"expose"=true})
	 */
	public function getMapHintContentaction($id)
	{
		$em    = $this->getDoctrine()->getManager();
		$coord = $this->getDoctrine()->getRepository('VidalMainBundle:MapCoord')->findOneByOfferId($id);
		if ($coord->getTitle() == '' or $coord->getTitle() == null) {
			$html = @file_get_contents('http://apteka.ru/_action/DrugStore/getMapHintContent/' . $id . '/');
			$html = preg_replace('#<a.*>.*</a>#USi', '', $html);
			$coord->setTitle($html);
			$em->flush($coord);
		}
		else {
			$html = $coord->getTitle();
		}
		return new Response($html);
	}

	/**
	 * @Route("/getMapBalloonContent/{id}", name="getMapBalloonContent", options={"expose"=true})
	 */
	public function getMapBalloonContent($id)
	{
		$em    = $this->getDoctrine()->getManager();
		$coord = $this->getDoctrine()->getRepository('VidalMainBundle:MapCoord')->findOneByOfferId($id);
		if ($coord->getText() == '' or $coord->getText() == null) {
			$html = @file_get_contents('http://apteka.ru/_action/DrugStore/getMapBalloonContent/' . $id . '/');
			$html = preg_replace('/Аптека не относится к выбранному региону/', '', $html);
			$html = preg_replace('#<a.*>.*</a>#USi', '', $html);
			$coord->setText($html);
			$em->flush($coord);
		}
		else {
			$html = $coord->getTitle();
		}
		return new Response($html);
	}
}
