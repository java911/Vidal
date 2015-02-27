<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Vidal\MainBundle\Entity\Digest;

/** @Secure(roles="ROLE_ADMIN") */
class DigestController extends Controller
{
	/**
	 * @Route("/delivery", name="delivery")
	 * @Template("VidalMainBundle:Digest:delivery.html.twig")
	 */
	public function deliveryAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager();
		$digest = $em->getRepository('VidalMainBundle:Digest')->get();

		$form = $this->createFormBuilder($digest)
			->add('text', null, array('label' => 'Текст письма', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
			->add('subject', null, array('label' => 'Тема письма', 'required' => true))
			->add('specialties', null, array('label' => 'Специальности', 'required' => false))
			->add('allSpecialties', null, array('label' => 'Всем специальностям', 'required' => false))
			->add('font', null, array('label' => 'Название шрифта без кавычек', 'required' => true))
			->add('emails', null, array('label' => 'Тестовые e-mail через ;', 'required' => false))
			->add('test', 'submit', array('label' => 'Разослать на тестовые', 'attr' => array('class' => 'btn-red')))
			->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn-red')))
			->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em->flush();
			$formData = $request->request->get('form');

			if (isset($formData['test'])) {
				$emails = isset($formData['emails']) ? explode(';', $formData['emails']) : array();
				$this->testTo($emails, $digest);
				$this->get('session')->getFlashBag()->add('test', true);
			}

			return $this->redirect($this->generateUrl('delivery'));
		}

		$params = array(
			'title'  => 'Рассылка писем',
			'digest' => $digest,
			'form'   => $form->createView(),
		);

		return $params;
	}

	private function testTo($emails, $digest)
	{
		$service = $this->get('email.service');
		$em      = $this->getDoctrine()->getManager();

		foreach ($emails as $email) {
			$email = trim($email);
			$user  = $em->getRepository('VidalMainBundle:User')->findOneByUsername($email);

			if ($user) {
				$service->send(
					$email,
					array('VidalMainBundle:Email:digest.html.twig', array('digest' => $digest, 'user' => $user)),
					$digest->getSubject()
				);
			}
		}
	}
}
