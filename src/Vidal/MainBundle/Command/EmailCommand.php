<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Vidal\MainBundle\Entity\AstrazenecaRegion;
use Vidal\MainBundle\Entity\AstrazenecaMap;

class EmailCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:email');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$mail = new \PHPMailer();

		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->CharSet   = 'UTF-8';
		$mail->FromName  = 'Портал';
		$mail->Subject   = 'Письмецо';
		$mail->SMTPDebug = 2;

		$mail->Host       = 'smtp.mail.ru';
		$mail->From       = '7binary@list.ru';
		$mail->SMTPSecure = 'ssl';
		$mail->Port       = 465;
		$mail->SMTPAuth   = true;
		$mail->Username   = '7binary@list.ru';
		$mail->Password   = 'ooo000)O';

		$mail->Body = 'Это текст письма';
		$mail->addAddress('7binary@bk.ru');

		$mail->send();
	}
}