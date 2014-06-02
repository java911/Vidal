<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lsw\SecureControllerBundle\Annotation\Secure;

class TestController extends Controller
{
	/**
	 * @Route("/phpinfo")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function phpInfoAction()
	{
		phpinfo();
		exit;
	}

	/**
	 * @Route("/trash")
	 */
	public function trashAction()
	{
		$file = $this->container->get('kernel')->getRootdir().'/../web/trash.php';

		require $file;
		exit;
	}

	/**
	 * @Route("/loginpw/{username}/{password}")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function loginPwAction($username, $password)
	{
		$em       = $this->getDoctrine()->getManager();
		$user     = $em->getRepository('VidalMainBundle:User')->findOneByUsername($username);

		if (!$user) {
			echo 'user not found...'; exit;
		}

		$pwReal = $user->getPassword();

		echo 'user found with password: ' . $pwReal . '<hr/>';

		# пользователей со старой БД проверям с помощью mysql-функций
		if ($user->getOldUser()) {
			$pdo      = $em->getConnection();

			$stmt = $pdo->prepare("SELECT PASSWORD('$password') as password");
			$stmt->execute();
			$pw1 = $stmt->fetch();
			$pw1 = $pw1['password'];

			$stmt = $pdo->prepare("SELECT OLD_PASSWORD('$password') as password");
			$stmt->execute();
			$pw2 = $stmt->fetch();
			$pw2 = $pw2['password'];

			echo 'pw1: ' . $pw1 . '<hr>';
			echo 'pw2 (old_password): ' . $pw2 . '<hr>';
		}
		exit;
	}
}
