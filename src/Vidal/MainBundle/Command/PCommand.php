<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:p')
			->setDescription('Send digest to concrete emails')
			->addArgument('emails', InputArgument::IS_ARRAY, 'Send digest to concrete emails');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('max_input_time', 0);
		ini_set('memory_limit', -1);

		$container  = $this->getContainer();
		$em         = $container->get('doctrine')->getManager();
		$templating = $container->get('templating');
		$subject    = 'Медицинский центр диагностики приглашает врачей к работе';
		$html       = $templating->render('VidalMainBundle:Digest:preview.html.twig');
		$emails     = $input->getArgument('emails');

		foreach ($emails as $email) {
			$this->send($email, $email, $html, $subject, true);
		}
	}

	public function send($email, $to, $body, $subject, $local = false)
	{
		$mail = new \PHPMailer();

		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->CharSet  = 'UTF-8';
		$mail->From     = 'maillist@vidal.ru';
		$mail->FromName = 'Портал «Vidal.ru»';
		$mail->Subject  = $subject;
		$mail->Host     = '127.0.0.1';
		$mail->Body     = $body;
		$mail->addAddress($email, $to);
		$mail->addCustomHeader('Precedence', 'bulk');

		if ($local) {
			$mail->Host       = 'smtp.yandex.ru';
			$mail->From       = 'binacy@yandex.ru';
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 465;
			$mail->SMTPAuth   = true;
			$mail->Username   = 'binacy@yandex.ru';
			$mail->Password   = 'oijoijoij';
		}

		$result = $mail->send();
		$mail   = null;

		return $result;
	}
}
