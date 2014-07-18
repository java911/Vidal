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
		 		u.jobAchievements, u.jobPublications, u.about
		 	FROM VidalMainBundle:User u
		 	LEFT JOIN u.specialization s
		 	LEFT JOIN u.primarySpecialty ps
		 	LEFT JOIN u.secondarySpecialty ss
		 	LEFT JOIN u.city c
		 	LEFT JOIN u.university uni
		 	LEFT JOIN u.region re
		 	LEFT JOIN u.country co
		 	ORDER BY u.id ASC
		')->getResult();

		return $users;
	}
}