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
			->add('subject', null, array('label' => 'Тема письма', 'required' => true))
			->add('specialties', null, array('label' => 'Специальности', 'required' => false))
			->add('text', null, array('label' => 'Текст письма', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
			->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn-red')))
			->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em->flush();

			return $this->redirect($this->generateUrl('delivery'));
		}

		$params = array(
			'title'  => 'Рассылка писем',
			'digest' => $digest,
			'form'   => $form->createView(),
		);

		return $params;
	}
}
