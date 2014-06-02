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
		$output->writeln('--- vidal:email started');

		$this->container  = $container = $this->getContainer();
		$em               = $container->get('doctrine')->getManager();
		$this->templating = $templating = $container->get('templating');

		# получаем список адресов по рассылке
		$doctors = $em->createQuery('
			SELECT u.username
			FROM VidalMainBundle:User u
		')->getResult();

		$emails = array();
		foreach ($doctors as $doctor) {
			$emails[] = $doctor['username'];
		}

		# разметка дайджеста
		$subject = 'Тестовая рассылка';
		$html    = $templating->render('VidalMainBundle:Email:email.html.twig');

		#testing
		$emails = array('7binary@bk.ru');

		# рассылка по 100 пользователям за цикл
		for ($i = 0, $c = count($emails); $i < $c; $i++) {
			$result = $this->send($emails[$i], $html, $subject);

			if ($i && ($i % 50) == 0) {
				sleep(30);
			}
		}

		$output->writeln('+++ vidal:email completed!');
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