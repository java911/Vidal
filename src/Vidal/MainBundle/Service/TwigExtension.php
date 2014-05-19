<?php
namespace Vidal\MainBundle\Service;

class TwigExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'learning_twig_extension';
	}

	/**
	 * Return the functions registered as twig extensions
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'is_file'         => new \Twig_Function_Method($this, 'is_file'),
			'dateFromMinutes' => new \Twig_Function_Method($this, 'dateFromMinutes'),
			'evrikaImg'       => new \Twig_Function_Method($this, 'evrikaImg'),
			'formatDate'      => new \Twig_Function_Method($this, 'formatDate'),
			'getClass'        => new \Twig_Function_Method($this, 'getClass'),
		);
	}

	/**
	 * Дополнительные фильтры
	 */
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('dateRu', array($this, 'dateRu')),
			new \Twig_SimpleFilter('shortcut', array($this, 'shortcut')),
			new \Twig_SimpleFilter('dateCreated', array($this, 'dateCreated')),
			new \Twig_SimpleFilter('upperFirst', array($this, 'upperFirst')),
			new \Twig_SimpleFilter('ucwords', array($this, 'ucwords')),
			new \Twig_SimpleFilter('type', array($this, 'type')),
		);
	}

	/**
	 * Вытаскивает и преобразует URL картинки из новостей EVRIKA
	 */
	public function evrikaImg($file)
	{
		if ($file == null) {
			return null;
		}
		elseif ($file == 'mynews') {
			return $file;
		}
		$array = unserialize($file);
		return 'http://evrika.ru' . $array['path'];
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

	public function dateFromMinutes($min)
	{
		$inputSeconds = $min * 60;

		$secondsInAMinute = 60;
		$secondsInAnHour  = 60 * $secondsInAMinute;
		$secondsInADay    = 24 * $secondsInAnHour;

		// extract days
		$days = floor($inputSeconds / $secondsInADay);

		// extract hours
		$hourSeconds = $inputSeconds % $secondsInADay;
		$hours       = floor($hourSeconds / $secondsInAnHour);

		// extract minutes
		$minuteSeconds = $hourSeconds % $secondsInAnHour;
		$minutes       = floor($minuteSeconds / $secondsInAMinute);

		// extract the remaining seconds
		$remainingSeconds = $minuteSeconds % $secondsInAMinute;
		$seconds          = ceil($remainingSeconds);

		return (int) $days . 'д ' . (int) $hours . 'ч ' . (int) $minutes . 'м';
	}

	public function formatDate($date, $showYear = true)
	{
		if (!$date) {
			return '';
		}

		$months = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

		return $date->format('d') . ' ' . $months[intval($date->format('m'))] . ' ' . $date->format('Y');
	}

	public function dateRu($date, $fullYear = false)
	{
		if (!$date) {
			return '';
		}

		$months  = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
		$dateStr = $date->format('d') . '&nbsp;' . $months[intval($date->format('m'))];

		if ($fullYear === true) {
			$dateStr .= '&nbsp;года';
		}
		elseif ($fullYear !== null) {
			$dateStr .= '&nbsp;' . $date->format('Y');
		}

		return $dateStr;
	}

	public function dateCreated($date)
	{
		if (!$date) {
			return '';
		}

		$months = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

		return $date->format('d') . ' ' . $months[intval($date->format('m'))] . ' в ' . $date->format('H:i');
	}

	public function shortcut($str, $max)
	{
		return mb_strlen($str, 'UTF-8') > $max
			? mb_substr($str, 0, $max, 'UTF-8') . '...'
			: $str;
	}

	public function getClass($object)
	{
		$reflect = new \ReflectionClass($object);

		return $reflect->getShortName();
	}

	public function upperFirst($string, $encoding = 'utf-8')
	{
		$strlen    = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then      = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	public function ucwords($str)
	{
		return mb_convert_case($str, MB_CASE_TITLE, 'utf-8');
	}

	public function type($object)
	{
		$reflect = new \ReflectionClass($object);

		return $reflect->getShortName();
	}
}