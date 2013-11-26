<?php
namespace Vidal\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class TwigExtension extends \Twig_Extension
{
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getName()
	{
		return 'vidal_twig_extension';
	}

	/**
	 * Return the functions registered as twig extensions
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'is_file' => new \Twig_Function_Method($this, 'is_file'),
		);
	}

	/**
	 * Дополнительные фильтры
	 */
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('engName', array($this, 'engName')),
			new \Twig_SimpleFilter('stripLoz', array($this, 'stripLoz')),
		);
	}

	/**
	 * Проверить из твига наличие файла (слеши из начала и конца убираются)
	 *
	 * @param string $filename
	 * @return bool
	 */
	public function is_file($filename)
	{
		return file_exists(trim($filename, '/'));
	}

	public function engName($str)
	{
		$p = array('/<SUP>&reg;<\/SUP>/', '/<SUP>&trade;<\/SUP>/', '/ /');
		$r = array('', '', '-');
		$str = preg_replace($p, $r, $str);

		return strtolower($str);
	}

	public function stripLoz($str)
	{
		return preg_replace('/&loz;/', '', $str);
	}
}