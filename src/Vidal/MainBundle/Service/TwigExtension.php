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
			new \Twig_SimpleFilter('dateDetailed', array($this, 'dateDetailed')),
			new \Twig_SimpleFilter('dateAgo', array($this, 'dateAgo')),
			new \Twig_SimpleFilter('shortcut', array($this, 'shortcut')),
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

	public function dateDetailed($date)
	{
		if (!$date) {
			return '';
		}

		$months = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

		return $date->format('d') . ' ' . $months[intval($date->format('m'))] . ' в ' . $date->format('H') . ':' . $date->format('i');
	}

	public function dateAgo(\DateTime $dateTime)
	{
		$delta = time() - $dateTime->getTimestamp();
		if ($delta < 0) {
			return '';
		}

		if ($delta <= 3600) {
			$time  = floor($delta / 60);
			$fract = $time % 10;
			if ($time == 1) {
				$duration = 'минуту';
			}
			elseif ($time > 5 && $time < 21) {
				$duration = $time . ' минут';
			}
			elseif ($fract == 1) {
				$duration = $time . ' минуту';
			}
			elseif ($fract > 1 && $fract < 5) {
				$duration = $time . ' минуты';
			}
			else {
				$duration = $time . ' минут';
			}
		}
		elseif ($delta <= 86400) {
			$time  = floor($delta / 3600);
			$fract = $time % 10;
			if ($time == 1) {
				$duration = 'час';
			}
			elseif ($time > 5 && $time < 21) {
				$duration = $time . ' часов';
			}
			elseif ($fract == 1) {
				$duration = $time . ' час';
			}
			elseif ($fract > 1 && $fract < 5) {
				$duration = $time . ' часа';
			}
			else {
				$duration = $time . ' часов';
			}
		}
		elseif ($delta <= 2592000) {
			$time  = floor($delta / 86400);
			$fract = $time % 10;
			if ($time == 1) {
				$duration = 'день';
			}
			elseif ($time > 5 && $time < 21) {
				$duration = $time . ' дней';
			}
			elseif ($fract == 1) {
				$duration = $time . ' день';
			}
			elseif ($fract > 1 && $fract < 5) {
				$duration = $time . ' дня';
			}
			else {
				$duration = $time . ' дней';
			}
		}
		else {
			return $this->dateDetailed($dateTime);
		}

		$duration .= ' назад';

		return $duration;
	}

	/**
	 * Фильтр обрезает строку, если больше $max и подставляет ...
	 */
	public function shortcut($str, $max)
	{
		$str = strip_tags($str);

		return mb_strlen($str, 'UTF-8') > $max
			? mb_substr($str, 0, $max, 'UTF-8') . '...'
			: $str;
	}
}