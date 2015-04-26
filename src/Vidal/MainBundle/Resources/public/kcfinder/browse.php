<?php

/** This file is part of KCFinder project
  *
  *      @desc Browser calling script
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

require "core/autoload.php";
$browser = new browser();

if (!isset($_SESSION['_sf2_attributes']['_security_everything'])
	|| !preg_match('/(ROLE_ADMIN|ROLE_SUPERADMIN)/', $_SESSION['_sf2_attributes']['_security_everything'])) {
	echo '<h1>Доступ к разделу запрещен</h1>';

	echo "<pre>";
	print_r($_SESSION);
	echo "</pre>";

	exit;
}

$browser->action();

?>