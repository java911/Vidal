<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RedirectController extends Controller
{
	/**
	 * @Route("/reckittbenckiser/{url}", requirements={"url"=".+"})
	 */
	public function redirectIndex()
	{
		return $this->redirect($this->generateUrl('index'), 301);
	}

	/** @Route("/Vidal/partneram/podpisnaya-kompaniya-SV/") */
	public function r1()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'spravochnik-vidal')), 301);
	}

	/** @Route("/Vidal/partneram/marketing-Vidal-Specialist/") */
	public function r2()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vidal-specialist')), 301);
	}

	/** @Route("/Vidal/partneram/email-mailing/") */
	public function r3()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'email-mailing')), 301);
	}

	/** @Route("/Vidal/partneram/basi-dannih-vrachi-sng/") */
	public function r4()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vrachi-sng')), 301);
	}

	/** @Route("/Vrachi-Rossii/") */
	public function r5()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vrachi-rossii')), 301);
	}

	/** @Route("/Vidal/partneram/Vidal-Vizit/") */
	public function r6()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'vidal-vizit')), 301);
	}

	/** @Route("/Vidal/partneram/Vidal-Vizit/") */
	public function r7()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'cd-versiya')), 301);
	}

	/** @Route("/Vidal/partneram/Kontakti-kommercheskii-otdel/") */
	public function r8()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'kommercheskii-otdel')), 301);
	}

	/** @Route("/Vidal/partneram/Krames-obucheniye-patients/") */
	public function r9()
	{
		return $this->redirect($this->generateUrl('about', array('url' => 'obucheniye')), 301);
	}

	/** @Route("/Vidal/vidal-russia/Novosti-pharmatsevticheskih-kompanii/") */
	public function r10()
	{
		return $this->redirect($this->generateUrl('pharma_news'), 301);
	}

	/**
	 * @Route("/Vidal/vidal-russia/{url}", requirements={"url"=".+"})
	 */
	public function r11($url)
	{
		$url = trim($url, '/');

		return $this->redirect($this->generateUrl('about', array('url' => $url)), 301);
	}

	/** @Route("/o-nas/email-mailing") */
	public function r12()
	{
		return $this->redirect($this->generateUrl('services_item', array('url' => 'email-mailing')), 301);
	}

	/** @Route("/astrazeneca/map") */
	public function r13()
	{
		 return $this->redirect($this->generateUrl('astrazeneca_map'), 301);
	}
}