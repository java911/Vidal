<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataController extends Controller
{
	/**
	 * @Route("/data/products", name="data_products")
	 */
	public function productsAction()
	{
		$products = $this->getDoctrine()
			->getRepository('VidalMainBundle:Product')
			->findAllNames();

		# отсекаем по регулярке все теги
		$pat = array('/<br\\/?>/i', '/<\\/?su(p|b)>/i', '/&.+;/i');
		$rep = array('', '', '');

		$productNames = [];
		$fileName = __DIR__ . DIRECTORY_SEPARATOR . 'vidal.products.txt';

		foreach ($products as $product) {
			$productNames[] = preg_replace($pat, $rep, $product['RusName']);
		}

		$productNames = array_unique($productNames);

		foreach ($productNames as $name) {
			file_put_contents($fileName, $name . PHP_EOL, FILE_APPEND);
		}

		echo 'completed';
		exit;
	}
}
