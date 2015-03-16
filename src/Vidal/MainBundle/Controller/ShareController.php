<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShareController extends Controller
{
	public function counterAction($class, $id)
	{
		$em     = $this->getDoctrine()->getManager('drug');
	}

	/** @Route("/share", name="share", options={"expose":true}) */
	public function shareAction(Request $request)
	{
		$my      = trim($request->request->get('my', ''));
		$friends = $request->request->get('friends', null);
		$text    = $request->request->get('text', null);

		if (!empty($my) && !empty($friends) && filter_var($request->request->get('my'), FILTER_VALIDATE_EMAIL)) {

			$emails = explode(';', $friends);
			$to     = array();

			# проверяем валидность адресов
			foreach ($emails as $email) {
				$email = trim($email);
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					return new JsonResponse('FAIL');
				}
				$to[] = $email;
			}

			$url   = $request->request->get('url', '');
			$title = urldecode($request->request->get('title', ''));

			# предотвратить отправку нескольких
			if ($this->get('prevent')->doubleClick()) {
				return new JsonResponse('DoubleClick');
			}

			# рассылаем
			$this->get('email.service')->send(
				$to,
				array('VidalMainBundle:Email:share.html.twig', array(
					'text'  => $text,
					'url'   => $url,
					'title' => $title,
				)),
				$my . ' поделился(-ась) с Вами: ' . $title,
				$my,
				false,
				$my
			);

			return new JsonResponse(implode('; ', $to));
		}

		return new JsonResponse('FAIL');
	}
}
