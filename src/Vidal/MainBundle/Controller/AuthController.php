<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Vidal\MainBundle\Entity\User;
use Vidal\MainBundle\Form\Type\RegisterType;

class AuthController extends Controller
{
	const DISPLAYED_CITIES_AJAX = 10;

	/**
	 * @Route("/registration", name="registration")
	 * @Template()
	 */
	public function registrationAction(Request $request)
	{
		if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirect($this->generateUrl('index'));
		}

		$em   = $this->getDoctrine()->getManager();
		$user = new User();
		$form = $this->createForm(new RegisterType($em), $user);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$password_orig = $user->getPassword();
			$user->setHash($this->calculateHash($user));

			$em->persist($user);
			$em->flush();
			$em->refresh($user);
			$this->resetToken($user);

			# уведомление пользователя о регистрации
			$this->get('email.service')->send(
				$user->getUsername(),
				array('VidalMainBundle:Email:registration.html.twig', array(
					'user'          => $user,
					'password_orig' => $password_orig
				)),
				'Благодарим за регистрацию на нашем портале!'
			);

			# уведомление администраторов о регистрации
			$this->get('email.service')->send(
				$this->container->getParameter('manager_emails'),
				array('VidalMainBundle:Email:registration_notice.html.twig', array('user' => $user)),
				'Зарегистрировался новый пользователь'
			);

			return $this->redirect($this->generateUrl('edit_profile'));
		}

		return array(
			'form'  => $form->createView(),
			'title' => 'Регистрация',
		);
	}

	/**
	 * @Route("/profile", name="profile")
	 * @Template()
	 */
	public function profileAction()
	{
		return array();
	}

	/**
	 * @Route("/eula", name="eula")
	 * @Template()
	 */
	public function eulaAction()
	{
		return array();
	}

	/**
	 * Пока у нас не используется encoder будем по-простому
	 */
	private function calculateHash($user)
	{
		return md5(time() . $user->getUsername() . $user->getPassword());
	}

	private function resetToken($user)
	{
		$token = new UsernamePasswordToken($user->getUsername(), $user->getPassword(), 'everything');
		$token->setUser($user);
		$this->get('security.context')->setToken($token);
	}

	private function generatePassword()
	{
		return substr(chr(rand(103, 122)) . chr(rand(103, 122)) . chr(rand(103, 122)) . md5(time() + rand(100, 999) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122))), 0, 8);
	}

	/**
	 * [AJAX] Логин через асинхронный запрос
	 * @Route("/ajax/login", name="ajax_login", options={"expose"=true})
	 */
	public function ajaxLoginAction(Request $request)
	{
		$username = $request->request->get('_username');
		$password = $request->request->get('_password');

		$user = $this->getDoctrine()->getRepository('VidalMainBundle:User')->findOneByUsername($username);

		if (!$user || $user->getPassword() !== $password) {
			return new JsonResponse(array('success' => 'no'));
		}

		$this->resetToken($user);

		return new JsonResponse(array('success' => 'yes'));
	}

	/**
	 * [AJAX] Автозаполнение городов
	 * @Route("/ajax/city", name="ajax_city", options={"expose"=true})
	 */
	public function ajaxCityAction(Request $request)
	{
		if ($request->query->has('term') == false) {
			return new JsonResponse();
		}

		$str = $request->query->get('term');
		$em  = $this->getDoctrine()->getManager();
		$str = '%' . $str . '%';

		$cities = $em->createQuery('SELECT c FROM VidalMainBundle:City c WHERE c.title LIKE :letter ORDER BY c.title ASC')
			->setParameter('letter', $str)
			->setFirstResult(0)
			->setMaxResults(self::DISPLAYED_CITIES_AJAX)
			->getResult();

		$citiesArray = array();

		foreach ($cities as $city) {
			$title = $city->getTitle();
			if ($country = $city->getCountry()) {
				$title .= ', ' . $country->getTitle();
			}
			$citiesArray[] = $title;
		}

		return new JsonResponse($citiesArray);
	}
}
