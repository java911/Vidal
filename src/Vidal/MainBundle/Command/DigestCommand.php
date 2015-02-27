<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DigestCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:digest')
			->setDescription('Send digest to users')
			->addOption('test', null, InputOption::VALUE_NONE, 'Send digest to manager e-mails')
			->addOption('stop', null, InputOption::VALUE_NONE, 'Stop sending digests')
			->addOption('clean', null, InputOption::VALUE_NONE, 'Clean log app/logs/digest_sent.txt')
			->addOption('all', null, InputOption::VALUE_NONE, 'Send digest to every subscribed user')
			->addOption('me', null, InputOption::VALUE_NONE, 'Send digest to 7binary@gmail.com')
			->addOption('local', null, InputOption::VALUE_NONE, 'Send digest from 7binary@list.ru');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('max_input_time', 0);
		ini_set('memory_limit', -1);

		# опции не указаны - выводим мануал
		if (!$input->getOption('test') && !$input->getOption('clean') && !$input->getOption('all') && !$input->getOption('me') && !$input->getOption('me')) {
			$output->writeln('=> Error: uncorrect syntax. READ BELOW');
			$output->writeln('$ php app/console evrika:digest --test');
			$output->writeln('$ php app/console evrika:digest --stop');
			$output->writeln('$ php app/console evrika:digest --clean');
			$output->writeln('$ php app/console evrika:digest --all');
			$output->writeln('$ php app/console evrika:digest --me');
			$output->writeln('$ php app/console evrika:digest --me --local');
			return false;
		}

		$container = $this->getContainer();
		$em        = $container->get('doctrine')->getManager();
		$digest    = $em->getRepository('VidalMainBundle:Digest')->get();

		# --stop   остановка рассылки дайджеста
		if ($input->getOption('stop')) {
			$digest->setProgress(false);
			$em->flush();
			$output->writeln('=> digest STOPPED');
			return true;
		}

		# если рассылка уже идет - возвращаем false
		if ($digest->getProgress()) {
			$output->writeln("=> ERROR: digest IN PROGRESS");
			return false;
		}

		# рассылка всем подписанным врачам
		if ($input->getOption('all')) {
			$output->writeln("Sending: in progress to ALL subscribed users...");
			$digest->setProgress(true);
			$this->sendToAll();
		}

		# рассылка нашим менеджерам
		if ($input->getOption('test')) {
			$raw      = explode(';', $digest->getEmails());
			$emails[] = array();

			foreach ($raw as $email) {
				$emails[] = trim($email);
			}

			$output->writeln("Sending: in progress to managers: " . implode(', ', $emails));
			$this->sendTo($emails);
		}

		# отправить самому себе
		if ($input->getOption('me')) {
			$output->writeln("Sending: in progress to 7binary@gmail.com");
			$this->sendTo(array('7binary@gmail.com'), $input->getOption('local'));
		}

		if ($input->getOption('clean')) {
			$em->createQuery('UPDATE VidalMainBundle:User u SET u.send=0 WHERE u.send=1')->execute();
			$output->writeln('Cleaned sent flag of users!');
		}

		return true;
	}

	private function sendToAll()
	{
		$container  = $this->getContainer();
		$em         = $container->get('doctrine')->getManager();
		$templating = $container->get('templating');
		$digest     = $em->getRepository('VidalMainBundle:Digest')->get();

		$users = $em->createQuery("
			SELECT u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created, u.firstName
			FROM VidalMainBundle:User u
			WHERE u.send = 0
			ORDER BY u.id ASC
		")->getResult();

		$subject     = $digest->getSubject();
		$template1   = $templating->render('VidalMainBundle:Digest:template1.html.twig', array('digest' => $digest));
		$updateQuery = $em->createQuery('UPDATE VidalMainBundle:User u SET u.send=1 WHERE u.id = :id');

		# рассылка
		$step = 40;

		for ($i = 0, $c = count($users); $i < $c; $i = $i + $step) {
			$users100 = array_slice($users, $i, $step);

			foreach ($users100 as $user) {
				$template2 = $templating->render('VidalMainBundle:Digest:template2.html.twig', array('user' => $user));
				$template  = $template1 . $template2;

				$this->send($user['username'], $user['firstName'], $template, $subject);
				$updateQuery->setParameter('id', $user['id'])->execute();
			}

			sleep(60);
		}
	}

	/**
	 * Рассылка по массиву почтовых адресов без логирования
	 *
	 * @param array $emails
	 */
	private function sendTo(array $emails, $local = false)
	{
		$container  = $this->getContainer();
		$em         = $container->get('doctrine')->getManager();
		$templating = $container->get('templating');
		$digest     = $em->getRepository('VidalMainBundle:Digest')->get();

		$users = $em->createQuery("
			SELECT u.username, u.id, DATE_FORMAT(u.created, '%Y-%m-%d_%H:%i:%s') as created, u.firstName
			FROM VidalMainBundle:User u
			WHERE u.username IN (:emails)
		")->setParameter('emails', $emails)
			->getResult();

		$subject   = $digest->getSubject();
		$template1 = $templating->render('VidalMainBundle:Digest:template1.html.twig', array('digest' => $digest));

		foreach ($users as $user) {
			$template2 = $templating->render('VidalMainBundle:Digest:template2.html.twig', array('user' => $user));
			$template  = $template1 . $template2;

			$this->send($user['username'], $user['firstName'], $template, $subject, $local);
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
			$mail->Host       = 'smtp.gmail.com';
			$mail->From       = 'binacy@gmail.com';
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 465;
			$mail->SMTPAuth   = true;
			$mail->Username   = 'binacy@gmail.com';
			$mail->Password   = '2q32q3q2';
		}

		$result = $mail->send();
		$mail   = null;

		return $result;
	}
}
