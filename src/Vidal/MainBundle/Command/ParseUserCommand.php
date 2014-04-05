<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\MainBundle\Entity\User;

class ParseUserCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:parse_user');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:parse_user started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$users = $this->findUsers();

		for ($i = 0; $i < count($users); $i++) {
			$user      = $users[$i];
			$u         = new User();
			$confirmed = $user['Confirmed'] == '0' ? false : true;

			$u->setPassword($user['Password']);
			$u->setEnabled($user['Checked']);
			$u->setCreated(new \DateTime($user['Created']));
			$u->setUpdated(new \DateTime($user['LastUpdated']));
			$u->setUsername($user['Email']);
			$u->setEmailConfirmed($confirmed);
			$u->setOldLogin($user['Login']);
			$u->setOldUser(true);
			$u->setFirstName($user['NameI']);
			$u->setLastName($user['NameF']);
			$u->setSurName($user['NameO']);
			$u->setOldCompany($user['Company']);

			if ($confirmed) {
				$u->setRoles('ROLE_DOCTOR');
			}

			$em->persist($u);
			$em->flush($u);

			if ($i && $i % 500 == 0) {
				$output->writeln("... +$i");
			}
		}

		$output->writeln('--- vidal:parse_user finished');
	}

	private function findUsers()
	{
		$pdo = $this->getContainer()->get('doctrine')->getManager('drug')->getConnection();

		$sql  = 'SELECT * from user';
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$usersRaw = $stmt->fetchAll();

		$users = array();

		for ($i = 0; $i < count($usersRaw); $i++) {
			$u = $usersRaw[$i];
			if (empty($u['Email'])) {
				continue;
			}

			$key = $usersRaw[$i]['Email'];
			if (!isset($users[$key])) {
				$users[$key] = $u;
			}
		}

		return array_values($users);
	}
}