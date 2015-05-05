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
	protected $container;

	protected $templating;

	protected function configure()
	{
		$this->setName('vidal:email');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 0);
		ini_set('set_time_limit', 0);

		for ($i = 1; $i <= 10; $i++) {
			$output->writeln($i);
			sleep(30);
		}

		$output->writeln('completed!');
	}

	public function send($email, $html, $subject)
	{
		$mail = new \PHPMailer();

		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->CharSet  = 'UTF-8';
		$mail->FromName = 'Портал Vidal.ru';
		$mail->Subject  = $subject;
		$mail->Body     = $html;
		$mail->addAddress($email);

		# prod - оптравка через Exim, dev/test - отправка через Gmail
		if ($this->container->getParameter('kernel.environment') == 'prod') {
			$mail->Host = '127.0.0.1';
			$mail->From = 'maillist@vidal.ru';
		}
		else {
			$mail->Host       = 'smtp.mail.ru';
			$mail->From       = '7binary@list.ru';
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 465;
			$mail->SMTPAuth   = true;
			$mail->Username   = '7binary@list.ru';
			$mail->Password   = 'ooo000)O';
		}

		$mail->send();
	}
}