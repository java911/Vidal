<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
	public function findOneByLogin($login)
	{
		return $this->_em->createQuery('
			SELECT u
			FROM VidalMainBundle:User u
			WHERE u.username = :login
				OR u.oldLogin = :login
		')->setParameter('login', $login)
			->getOneOrNullResult();
	}

	public function findUsersExcel()
	{
		$users = $this->_em->createQuery('
		 	SELECT u.username, u.lastName, u.firstName, u.surName,
		 		s.title as specialization, ps.title as primarySpecialty, ss.title as secondarySpecialty,
		 		c.title as city, re.title as region, co.title as country,
		 		uni.title as university, u.school,
		 		u.graduateYear, u.birthdate, u.academicDegree, u.phone, u.icq, u.educationType,
		 		u.dissertation, u.professionalInterests, u.jobPlace, u.jobSite, u.jobPosition, u.jobStage,
		 		u.jobAchievements, u.jobPublications, u.about, u.oldUser, u.created
		 	FROM VidalMainBundle:User u
		 	LEFT JOIN u.specialization s
		 	LEFT JOIN u.primarySpecialty ps
		 	LEFT JOIN u.secondarySpecialty ss
		 	LEFT JOIN u.city c
		 	LEFT JOIN u.university uni
		 	LEFT JOIN u.region re
		 	LEFT JOIN u.country co
		 	ORDER BY u.id ASC
		')
			->getResult();

		return $users;
	}

	public function forExcel($number = null)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select("s.title as specialty, c.title as city, r.title as region, DATE_FORMAT(u.created, '%Y-%m-%d') as registered, u.username, u.lastName, u.firstName, u.surName")
			->from('VidalMainBundle:User', 'u')
			->leftJoin('u.city', 'c')
			->leftJoin('c.region', 'r')
			->leftJoin('c.country', 'co')
			->leftJoin('u.primarySpecialty', 's')
			->orderBy('u.username', 'ASC');

		if ($number > 2000) {
			$created = new \DateTime("$number-01-01 00:00:00");
			$qb->where('u.created > :created')->setParameter('created', $created);
		}
		elseif ($number > 0 && $number <= 12) {
			$year  = date('Y');
			$month = date('m');
			if ($number > $month) {
				$year--;
			}
			$created   = new \DateTime("$year-$number-01 00:00:00");
			$nextMonth = new \DateTime("$year-$number-01 00:00:00");
			$nextMonth->modify('+1 month');
			$qb->where('u.created > :created')
				->andWhere('u.created < :nextMonth')
				->setParameter('created', $created)
				->setParameter('nextMonth', $nextMonth);
		}

		return $qb->getQuery()->getResult();
	}

	public function checkOldPassword($password, $pwReal)
	{
		$pdo = $this->_em->getConnection();

		$stmt = $pdo->prepare("SELECT PASSWORD('$password') as password");
		$stmt->execute();
		$pw1 = $stmt->fetch();
		$pw1 = $pw1['password'];

		$stmt = $pdo->prepare("SELECT OLD_PASSWORD('$password') as password");
		$stmt->execute();
		$pw2 = $stmt->fetch();
		$pw2 = $pw2['password'];

		return $pw1 === $pwReal || $pw2 === $pwReal;
	}

	public function total()
	{
		return $this->_em->createQuery('
			SELECT COUNT(u.id)
			FROM VidalMainBundle:User u
		')->getSingleScalarResult();
	}
}