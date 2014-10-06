<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vidal\MainBundle\Entity\AstrazenecaFaq;
use Vidal\MainBundle\Entity\AstrazenecaRegion;
use Vidal\MainBundle\Entity\AstrazenecaMap;
use Lsw\SecureControllerBundle\Annotation\Secure;

class AstrazenecaController extends Controller
{
	/**
	 * @Route("/shkola-gastrita", name="shkola_gastrita")
	 * @Template("VidalMainBundle:Astrazeneca:shkola.html.twig")
	 */
	public function shkolaAction(Request $request)
	{
		$params = array(
			'noYad'     => true,
			'title'     => 'Школа гастрита',
			'menu_left' => 'shkola',
		);

		$em                        = $this->getDoctrine()->getManager();
		$params['blogs']           = $em->getRepository('VidalMainBundle:AstrazenecaBlog')->findActive();
		$params['articles']        = $em->getRepository('VidalMainBundle:AstrazenecaNew')->findActive();
		$params['tests']           = $em->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();
		$params['questionAnswers'] = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findByEnabled(1);

		# форма задать вопрос
		$faq = new AstrazenecaFaq();

		$builder = $this->createFormBuilder($faq);
		$builder
			->add('authorFirstName', null, array('label' => 'Ваше имя', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Имя"))))
			->add('authorEmail', null, array('label' => 'Ваш e-mail', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Email"))))
			->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
			->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
			->add('submit', 'submit', array('label' => 'ОТПРАВИТЬ', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);

		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$faq->setEnabled(0);
				$em->persist($faq);
				$em->flush();

				$this->get('session')->getFlashBag()->add('questioned', '');

				return $this->redirect($this->generateUrl('shkola_gastrita') . '#qa');
			}
		}

		$params['form'] = $form->createView();

		return $params;
	}

	/**
	 * @Route("/shkola-gastrita2", name="shkola_gastrita2")
	 * @Template("VidalMainBundle:Astrazeneca:shkola.html.twig")
	 */
	public function shkola2Action(Request $request)
	{
		$params = array(
			'noYad'     => true,
			'title'     => 'Школа гастрита',
			'menu_left' => 'shkola',
		);

		$em                        = $this->getDoctrine()->getManager();
		$params['blogs']           = $em->getRepository('VidalMainBundle:AstrazenecaBlog')->findActive();
		$params['articles']        = $em->getRepository('VidalMainBundle:AstrazenecaNew')->findActive();
		$params['tests']           = $em->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();
		$params['questionAnswers'] = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findByEnabled(1);

		# форма задать вопрос
		$faq = new AstrazenecaFaq();

		$builder = $this->createFormBuilder($faq);
		$builder
			->add('authorFirstName', null, array('label' => 'Ваше имя', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Имя"))))
			->add('authorEmail', null, array('label' => 'Ваш e-mail', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Email"))))
			->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
			->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
			->add('submit', 'submit', array('label' => 'ОТПРАВИТЬ', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);

		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$faq->setEnabled(0);
				$em->persist($faq);
				$em->flush();

				$this->get('session')->getFlashBag()->add('questioned', '');

				return $this->redirect($this->generateUrl('shkola_gastrita') . '#qa');
			}
		}

		$params['form'] = $form->createView();

		return $params;
	}

	/**
	 * @Route("/shkola-gastrita-map", name="shkola_map")
	 * @Template("VidalMainBundle:Astrazeneca:frame_map.html.twig")
	 */
	public function frameMapAction()
	{
		return array();
	}

	/** @Route("/shkola-gastrita/video", name="astrazeneca_video") */
	public function videoAction()
	{
		return $this->redirect($this->generateUrl('shkola_gastrita'), 301);
	}

	/** @Route("/shkola-gastrita/articles", name="astrazeneca_news") */
	public function newsAction(Request $request)
	{
		return $this->redirect($this->generateUrl('shkola_gastrita'), 301);
	}

	/** @Route("/shkola-gastrita/article/{newId}", name="astrazeneca_new") */
	public function showNewAction($newId)
	{
		return $this->redirect($this->generateUrl('shkola_gastrita'), 301);
	}

	/** @Route("/shkola-gastrita/map", name="astrazeneca_map") */
	public function mapAction()
	{
		return $this->redirect($this->generateUrl('shkola_gastrita'), 301);
	}

	/**
	 * @Route("/shkola-gastrita/map-ajax", name="astrazeneca_map_xml", options={"expose"=true})
	 * @Template("VidalMainBundle:Astrazeneca:map_xml.html.twig")
	 */
	public function mapXmlAction()
	{
		$coords[0] = $this->getRequest()->query->get('x1');
		$coords[1] = $this->getRequest()->query->get('y1');
		$coords[2] = $this->getRequest()->query->get('x2');
		$coords[3] = $this->getRequest()->query->get('y2');
		$zoom      = $this->getRequest()->query->get('z');

		if ($zoom <= 5) {
			$coords = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaRegion')->findAll();
		}
		else {
			$coords = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findCoords($coords);
		}

		//		var_dump(count($coords));
		//		exit;

		return array(
			'coords'    => $coords,
			'noYad'     => true,
			'menu_left' => 'shkola',
		);
	}

	/**
	 * @Route("/shkola-gastrita/testing", name="astrazeneca_testing")
	 * @Template("VidalMainBundle:Astrazeneca:test.html.twig")
	 */
	public function testingAction(Request $request)
	{

		$tests = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();

		return array(
			'tests'     => $tests,
			'noYad'     => true,
			'title'     => 'Тестирование | Школа гастрита',
			'menu_left' => 'shkola',
		);
	}

	/**
	 * @Route("/shkola-gastrita/testing-ajax/{step}", name="astrazeneca_testing_ajax", options={"expose"=true})
	 */
	public function testingAjaxAction(Request $request, $step)
	{

		$question = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();
		if (isset($question[$step - 1])) {
			$question = $question[$step - 1];
		}
		else {
			$question = null;
		}

		return new Response($question->getTitle());
	}

	/**
	 * @Route("/shkola-gastrita/faq", name="astrazeneca_faq")
	 * @Template("VidalMainBundle:Astrazeneca:faq.html.twig")
	 */
	public function faqAction(Request $request)
	{
		$em  = $this->getDoctrine()->getManager();
		$faq = new AstrazenecaFaq();

		$builder = $this->createFormBuilder($faq);
		$builder
			->add('authorFirstName', null, array('label' => 'Ваше имя'))
			->add('authorEmail', null, array('label' => 'Ваш e-mail'))
			->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
			->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
			->add('submit', 'submit', array('label' => 'Задать вопрос', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);

		//@Assert\NotBlank(message="Пожалуйста, укажите Имя")
		$builder = $this->createFormBuilder($faq);
		$builder
			->add('authorFirstName', null, array('label' => 'Ваше имя', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Имя"))))
			->add('authorEmail', null, array('label' => 'Ваш e-mail', 'required' => true, 'constraints' => new NotBlank(array('message' => "Пожалуйста, укажите Email"))))
			->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
			->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
			->add('submit', 'submit', array('label' => 'Отправить', 'attr' => array('class' => 'btn')));
		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$faq->setEnabled(0);
				$em->persist($faq);
				$em->flush();
				$em->refresh($faq);
			}
		}

		return array(
			'title'           => 'Вопрос-ответ | Школа гастрита',
			'questionAnswers' => $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaFaq')->findByEnabled(1),
			'form'            => $form->createView(),
			'noYad'           => true,
			'menu_left'       => 'shkola',
		);
	}

	/**
	 * @Secure(roles="ROLE_ADMIN")
	 * @Route("/shkola-gastrita/admin/faq", name="admin_astrazeneca_faq")
	 * @Template("VidalMainBundle:Astrazeneca:admin_faq.html.twig")
	 */
	public function adminFaqListAction()
	{
		//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
		//            $this->redirect($this->generateUrl('index'));
		//        }
		$faqs = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaFaq')->findAll();
		return array(
			'faqs'      => $faqs,
			'noYad'     => true,
			'title'     => 'Вопрос-ответ | Школа гастрита',
			'menu_left' => 'shkola',
		);
	}

	/**
	 * @Secure(roles="ROLE_ADMIN")
	 * @Route("/shkola-gastrita/admin/faq/add", name="admin_astrazeneca_faq_add")
	 * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
	 */
	public function adminFaqAddAction(Request $request)
	{

		//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
		//            $this->redirect($this->generateUrl('index'));
		//        }

		$em  = $this->getDoctrine()->getManager();
		$faq = new AstrazenecaFaq();

		$builder = $this->createFormBuilder($faq);
		$builder
			->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
			->add('answer', null, array('label' => 'Ответ', 'attr' => array('class' => 'ckeditor')))
			->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);

		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$em->persist($faq);
				$em->flush();
			}
		}

		return array(
			'form'      => $form->createView(),
			'noYad'     => true,
			'title'     => 'Добавить Вопрос-ответ | Школа гастрита',
			'menu_left' => 'shkola',
		);
	}

	/**
	 * @Secure(roles="ROLE_ADMIN")
	 * @Route("/shkola-gastrita/admin/faq/{faqId}", name="admin_astrazeneca_faq_edit")
	 * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
	 */
	public function adminFaqEditAction(Request $request, $faqId)
	{

		//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
		//            $this->redirect($this->generateUrl('index'));
		//        }

		$em  = $this->getDoctrine()->getManager();
		$faq = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findOneById($faqId);

		$builder = $this->createFormBuilder($faq);
		$builder
			->add('question', null, array('label' => 'Вопрос'))
			->add('answer', null, array('label' => 'Ответ'))
			->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

		$form = $builder->getForm();
		$form->handleRequest($request);

		if ($request->isMethod('POST')) {
			if ($form->isValid()) {
				$faq = $form->getData();
				$em->flush($faq);
			}
		}
		return array(
			'form'      => $form->createView(),
			'noYad'     => true,
			'title'     => 'Редактировать Вопрос-ответ | Школа гастрита',
			'menu_left' => 'shkola',
		);
	}

	/**
	 * @Secure(roles="ROLE_ADMIN")
	 * @Route("/shkola-gastrita/admin/faq/delete/{faqId}", name="admin_astrazeneca_faq_delete")
	 * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
	 */
	public function adminFaqDeleteAction(Request $request, $faqId)
	{

		//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
		//            $this->redirect($this->generateUrl('index'));
		//        }

		$em  = $this->getDoctrine()->getManager();
		$faq = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findOneById($faqId);

		$em->remove($faq);
		$em->flush();

		return $this->redirect($this->generateUrl('astrazeneca_faq'));
	}

	/**
	 * @Route("/shkola-gastrita/zgetMapHintContent/{id}", name="zgetMapHintContent", options={"expose"=true})
	 */
	public function getMapHintContentaction($id)
	{
		$em    = $this->getDoctrine()->getManager();
		$coord = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findOneById($id);
		$html  = $coord->getTitle();
		return new Response($html);
	}

	/**
	 * @Route("/shkola-gastrita/zgetMapBalloonContent/{id}", name="zgetMapBalloonContent", options={"expose"=true})
	 */
	public function getMapBalloonContent($id)
	{
		$coord = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findOneById($id);
		$html  = $coord->getAdr();

		return new Response($html);
	}

}