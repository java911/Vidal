<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventSendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:eventsend')
            ->setDescription('Send event letter to emails')
            ->addArgument('emails', InputArgument::IS_ARRAY, 'Send digest to concrete emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer();
        $logger     = $container->get('vidal.digest_logger');
        $templating = $container->get('templating');
        $emails     = $input->getArgument('emails');

        $mailer           = $container->get('mailer');
        $mailer_transport = $container->get('swiftmailer.transport.real');
        $transport        = $mailer->getTransport();
        $spool            = $transport->getSpool();

        $mailer->registerPlugin(new \Swift_Plugins_AntiFloodPlugin(10));
        $mailer->registerPlugin(new \Swift_Plugins_AntiFloodPlugin(10, 2));

        $subject = 'Один раз увидеть';
        $html    = $templating->render('VidalMainBundle:Email:univadis_delivery.html.twig');

        $logger->openAppend();

        foreach ($emails as $email) {
            try {
                $headers = null;
                $msg     = null;

                # отправка сообщения и логирование
                $result = $this->send($email, $html, $subject);
                $result ? $logger->writeSentEmail($email) : $logger->writeFailEmail($email);

                # очищение очереди отправки сообщений
                $spool->flushQueue($mailer_transport);
            }
            catch (\Exception $e) {
                continue;
            }
        }

        $logger->close();
    }

    public function send($email, $body, $subject)
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
        $mail->addAddress($email);

        $result = $mail->send();
        $mail   = null;

        return $result;
    }
}
