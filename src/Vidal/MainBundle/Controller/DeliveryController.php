<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Vidal\MainBundle\Entity\Digest;

/** @Secure(roles="ROLE_ADMIN") */
class DeliveryController extends Controller
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
			->add('total', null, array('label' => 'Всего к отправке', 'required' => false, 'disabled' => true))
			->add('totalSend', null, array('label' => 'Уже отправлено', 'required' => false, 'disabled' => true))
			->add('totalLeft', null, array('label' => 'Осталось отправить', 'required' => false, 'disabled' => true))
			->add('limit', null, array('label' => 'Лимит писем', 'required' => false))
			->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn-red')))
			->add('test', 'submit', array('label' => 'Разослать на тестовые', 'attr' => array('class' => 'btn-red')))
			->add('clean', 'submit', array('label' => 'Обнулить разосланные', 'attr' => array('class' => 'btn-red')));

		if (true == $digest->getProgress()) {
			$form->add('stop', 'submit', array('label' => 'Остановить рассылку', 'attr' => array('class' => 'btn-red')));
		}
		else {
			$form->add('start', 'submit', array('label' => 'Запустить рассылку', 'attr' => array('class' => 'btn-red')));
		}

		$form = $form->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $request->request->get('form');

			if (isset($formData['clean'])) {
				$em->createQuery('UPDATE VidalMainBundle:User u SET u.send=0 WHERE u.send=1')->execute();
				$digest->setProgress(false);
				$this->get('session')->getFlashBag()->add('test', 'Разосланные обнулены');
			}

			if (isset($formData['stop'])) {
				$em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 0')->execute();
				$this->get('session')->getFlashBag()->add('test', 'Рассылка остановлена');

				return $this->redirect($this->generateUrl('delivery'));
			}

			if (isset($formData['start'])) {

				$em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 1')->execute();
				$this->get('session')->getFlashBag()->add('test', 'Рассылка запущена (в течении 5 минут начнется отправка)');

				# если команда уже не запущена, то запускаем на выполнение
				exec("/bin/ps -axw", $out);
				if (!preg_match('/vidal:digest --all/', implode(' ', $out))) {
					$cmd = 'nohup php ' . $this->get('kernel')->getRootDir() . '/console vidal:digest --all > /dev/null 2>&1 &';
					system($cmd);
				}

				return $this->redirect($this->generateUrl('delivery'));

			}

			if (isset($formData['start'])) {
				$em->createQuery('UPDATE VidalMainBundle:User u SET u.send=0 WHERE u.send=1')->execute();
				$em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 0')->execute();
				$em->flush();
				$this->get('session')->getFlashBag()->add('test', 'Обнулили разосланных и остановили рассылку');

				return $this->redirect($this->generateUrl('delivery'));
			}

			$specialties = $digest->getSpecialties();

			if (isset($formData['test'])) {
				$emails = isset($formData['emails']) ? explode(';', $formData['emails']) : array();
				$this->testTo($emails, $digest);
				$this->get('session')->getFlashBag()->add('test', 'Было отправлено на адреса: ' . $formData['emails']);
			}

			# считаем, сколько всего к отправке
			$qb = $em->createQueryBuilder();
			$qb->select("COUNT(u.id)")
				->from('VidalMainBundle:User', 'u')
				->andWhere('u.enabled = 1')
				->andWhere('u.emailConfirmed = 1')
				->andWhere('u.digestSubscribed = 1');

			if (count($specialties)) {
				$ids = array();
				foreach ($specialties as $specialty) {
					$ids[] = $specialty->getId();
				}
				$qb->andWhere('u.primarySpecialty IN (:ids) OR u.secondarySpecialty IN (:ids)')
					->setParameter('ids', $ids);
			}

			$total = $qb->getQuery()->getSingleScalarResult();
			$digest->setTotal($total);

			# считаем, сколько отправлено
			$totalSend = $em->createQuery('SELECT COUNT(u.id) FROM VidalMainBundle:User u WHERE u.send = 1')
				->getSingleScalarResult();
			$digest->setTotalSend($totalSend);

			# cчитаем, сколько осталось отправить
			$left = $digest->getTotal() - $digest->getTotalSend();
			$digest->setTotalLeft($left < 0 ? 0 : $left);

			$em->flush();

			$this->get('session')->getFlashBag()->add('test', 'Изменения сохранены');

			return $this->redirect($this->generateUrl('delivery'));
		}

		$params = array(
			'title'        => 'Рассылка писем',
			'digest'       => $digest,
			'form'         => $form->createView(),
			'total'        => $em->getRepository('VidalMainBundle:User')->total(),
			'subscribed'   => $em->getRepository('VidalMainBundle:Digest')->countSubscribed(),
			'unsubscribed' => $em->getRepository('VidalMainBundle:Digest')->countUnsubscribed(),
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
