<?php
namespace Vidal\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\TwigBundle\TwigEngine as Templating;

class EmailService
{
	private $container;
	private $templating;

	public function __construct(Container $container, Templating $templating)
	{
		$this->container  = $container;
		$this->templating = $templating;
	}

	public function send($emails, $template, $subject = 'Уведомление', $from = 'maillist@vidal.ru', $localhost = false, $fromName = false)
	{
		$mail = new \PHPMailer();

		$portal = $this->container->getParameter('portal');

		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->CharSet  = 'UTF-8';
		$mail->FromName = $fromName ? $fromName : 'Портал ' . $portal;
		$mail->Subject  = $subject;

		# prod - оптравка через почтовый сервер на серваке, dev/test - отправка через Mail.ru
		if ($this->container->getParameter('kernel.environment') == 'prod' || $localhost) {
			$mail->Host = '127.0.0.1';
			$mail->From = $from;
		}
		else {
			$mail->SMTPDebug  = 2;
			$mail->Host       = 'smtp.gmail.com';
			$mail->From       = '7binary@list.ru';
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 587;
			$mail->SMTPAuth   = true;
			$mail->Username   = '7binacy@gmail.com';
			$mail->Password   = 'ijoijojg';
		}

		# устанавливаем содержимое письма
		$templateParams = array('portal' => $portal);
		if (is_string($template)) {
			$templateName = $template;
		}
		else {
			$templateName   = $template[0];
			$templateParams = array_merge($templateParams, $template[1]);
		}

		$mail->Body = $this->templating->render($templateName, $templateParams);

		# устанавливаем получателя(ей) письма
		if (is_string($emails)) {
			$mail->addAddress($emails);
		}
		else {
			foreach ($emails as $email) {
				$mail->addAddress($email);
			}
		}

		$mail->send();
	}
}