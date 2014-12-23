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
	 * @Route("/t")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function tAction()
	{
		return $this->render('VidalMainBundle:Test:t.html.twig');
	}
}
