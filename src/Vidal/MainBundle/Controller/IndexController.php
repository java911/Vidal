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
use Vidal\MainBundle\Entity\QuestionAnswer;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Doctrine\ORM\EntityRepository;

class IndexController extends Controller
{
	const PUBLICATIONS_SHOW = 5;
	const ARTICLES_SHOW     = 4;

	/**
	 * @Route("/", name="index")
	 * @Template("VidalMainBundle:Index:index.html.twig")
	 */
	public function indexAction()
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$articles = $em->getRepository('VidalDrugBundle:Article')->findLast(self::ARTICLES_SHOW);

		if ($art = $em->getRepository('VidalDrugBundle:Art')->atIndex()) {
			$articles[] = $art;
			usort($articles, function ($a, $b) {
				$dateA = $a->getDate();
				$dateB = $b->getDate();

				if ($dateA < $dateB) {
					return 1;
				}
				elseif ($dateA > $dateB) {
					return -1;
				}
				else {
					return 0;
				}
			});
		}

		$params = array(
			'indexPage'    => true,
			'seotitle'     => 'Справочник лекарственных препаратов Видаль. Описание лекарственных средств',
			'publications' => $em->getRepository('VidalDrugBundle:Publication')->findLast(self::PUBLICATIONS_SHOW),
			'articles'     => $articles,
		);

		return $params;
	}

	/**
	 * @Route("/otvety_specialistov", name="qa")
	 * @Template("VidalMainBundle:Index:qa.html.twig")
	 */
	public function qaAction(Request $request)
	{
		$em  = $this->getDoctrine()->getManager();
		$faq = new QuestionAnswer();
		if ($this->getUser()) {
			$faq->setAuthorFirstName($this->getUser()->getFirstname());
			$faq->setAuthorEmail($this->getUser()->getUsername());
		}
		$builder = $this->createFormBuilder($faq);
		$builder
			->add('authorFirstName', null, array('label' => 'Ваше имя'))
			->add('authorEmail', null, array('label' => 'Ваш e-mail'))
            ->add('place', 'entity', array(
                'label'         => 'Область заболевания',
                'empty_value'   => 'выберите',
                'required'      => true,
                'class'         => 'VidalMainBundle:QuestionAnswerPlace',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.title', 'ASC');
                    }
            ))
			->add('question', null, array('label' => 'Вопрос'))
			->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
			->add('submit', 'submit', array('label' => 'Задать вопрос', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);
		$t = 0;
		if ($request->isMethod('POST')) {
			$t = 1;
			if ($form->isValid()) {
				$t   = 2;
				$faq = $form->getData();
				$faq->setEnabled(0);
				$em->persist($faq);
				$em->flush();
				$em->refresh($faq);

                $this->get('email.service')->send(
                    $this->container->getParameter('manager_emails'),
                    array('VidalMainBundle:Email:qa_question.html.twig', array('faq' => $faq)),
                    'Вопрос на сайте vidal.ru'
                );
			}
		}
		$qus = $this->getDoctrine()->getRepository('VidalMainBundle:QuestionAnswer')->findByEnabled(1);
		krsort($qus);

            $p = ceil(count($qus)/10);


        $qaPagination = $this->get('knp_paginator')->paginate(
            $qus,
            $request->query->get('p', $p),
            10
        );

		return array(
			'title'           => 'Ответы специалистов',
			'menu_left'       => 'qa',
			'questionAnswers' => $qus,
			'form'            => $form->createView(),
			't'               => $t,
            'qaPagination'    => $qaPagination
		);
	}

	/**
	 * @Route("/otvety_specialistov_doctor", name="qa_admin")
	 * @Secure(roles="ROLE_DOCTOR")
	 * @Template()
	 */
	public function doctorAnswerListAction(Request $request)
	{
		$questions = $this->getDoctrine()->getRepository('VidalMainBundle:QuestionAnswer')->findByAnswer(null);
		return array('questions' => $questions);
	}

	/**
	 * @Route("/otvety_specialistov_doctor/{faqId}", name="qa_admin_edit")
	 * @Secure(roles="ROLE_DOCTOR")
	 * @Template()
	 */
	public function doctorAnswerEditAction(Request $request, $faqId)
	{
		$em  = $this->getDoctrine()->getManager();
		$faq = $em->getRepository('VidalMainBundle:QuestionAnswer')->findOneById($faqId);
		if ($faq->getAnswer() == null) {
			$builder = $this->createFormBuilder($faq);
			$builder
				->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
				->add('answer', null, array('label' => 'Ответ', 'attr' => array('class' => 'ckeditor')))
				->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

			$form = $builder->getForm();
		}
		else {
			return $this->redirect($this->generateUrl('qa_admin'));
		}
		$form->handleRequest($request);

		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$faq->setEnabled(1);
				if ($faq->getAnswer() != null) {
					$em->flush($faq);

                    $this->get('email.service')->send(
                        $faq->getAuthorEmail(),
                        array('VidalMainBundle:Email:qa_answer.html.twig', array('faq' => $faq)),
                        'Ответ на сайте vidal.ru'
                    );

					return $this->redirect($this->generateUrl('qa_admin'));
				}
			}
		}
		return array('form' => $form->createView());
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
	 * Наши услуги
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
			'items'     => $this->getDoctrine()->getRepository('VidalMainBundle:AboutService')->findServices(),
		);

		return $params;
	}

	/**
	 * Наши услуги
	 * @Route("/services/{url}", name="services_item")
	 *
	 * @Template()
	 */
	public function servicesItemAction($url)
	{
		$about = $this->getDoctrine()->getRepository('VidalMainBundle:AboutService')->findOneByUrl($url);

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
	 * @Route("/kontakty-aptek", name="kontakty_aptek")
	 *
	 * @Template("VidalMainBundle:Index:kontaktyAptek.html.twig")
	 */
	public function kontaktyAptekAction()
	{
		return array('title' => 'Контакты аптек');
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

		return array(
			'title'    => 'Карта аптек',
			'menu'     => 'pharmacies_map',
			'cities'   => $cities,
			'thisCity' => $thisCities,
		);
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
