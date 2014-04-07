<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class InfoController extends Controller
{
	/**
	 * @Route("o-nas/Priobreteniye-spravochnikov", name="priobretenie")
	 * @Template()
	 */
	public function priobretenieAction()
	{
		return array();
	}
} 
