<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
	public function downloadAction(Request $request, $filename)
	{
		if (!$this->get('security.context')->isGranted('ROLE_DOCTOR')) {
			return $this->redirect($this->generateUrl('no_download', array('filename' => $filename)));
		}

		$contentType = 'application/octet-stream';
		$filename    = str_replace('/', '', $filename);

		if ($this->get('kernel')->getEnvironment() == 'dev') {
			$path = $this->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . '..'
				. DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR . $filename;
		}
		else {
			$path = '/home/twigavid/vidal/download/' . $filename;
		}

		if (preg_match('/^(.+)\\.zip$/i', $filename)) {
			$contentType = 'application/zip';
		}

		if (!file_exists($path)) {
			throw $this->createNotFoundException();
		}

		if ($filename == 'users.xls') {
			$em        = $this->getDoctrine()->getManager();
			$pw        = $request->query->get('pw', null);
			$hasAccess = $em->getRepository('VidalMainBundle:KeyValue')->checkMatch('users', $pw);

			if (!$hasAccess) {
				throw $this->createNotFoundException();
			}
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
