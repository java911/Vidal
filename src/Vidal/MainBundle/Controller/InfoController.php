<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

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

	/**
	 * @Route("/shkola-zdorovya/calculator.html", name="calculate")
	 * @Template("VidalMainBundle:Info:calculate.html.twig")
	 */
	public function calculateAction()
	{
		return array(
			'title' => 'Калькулятор подбора полезной воды'
		);
	}

	/**
	 * @Route("/download/{filename}", name="download")
	 */
	public function downloadAction($filename)
	{
		if (!$this->get('security.context')->isGranted('ROLE_DOCTOR')) {
			return $this->redirect($this->generateUrl('no_download', array('filename' => $filename)));
		}

		if ($filename == 'users.xls') {
			exit;
		}

		$filename    = str_replace('/', '', $filename);
		$path        = '/home/twigavid/vidal/download/' . $filename;
		$contentType = 'application/octet-stream';

		if (preg_match('/^(.+)\\.zip$/i', $filename)) {
			$contentType = 'application/zip';
		}

		if (!file_exists($path)) {
			throw $this->createNotFoundException();
		}

		header('X-Sendfile: ' . $path);
		header('Content-Type: ' . $contentType);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		exit;
	}

	/**
	 * @Route("/no-download/{filename}", name="no_download")
	 * @Template("VidalMainBundle:Info:no_download.html.twig")
	 */
	public function noDownloadAction($filename)
	{
		return array('filename' => $filename);
	}
}
