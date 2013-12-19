<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ATCRepository extends EntityRepository
{
	public function findOneByATCCode($ATCCode)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:ATC a
		 	WHERE a = :ATCCode
		')->setParameter('ATCCode', $ATCCode)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalMainBundle:ATC a
			JOIN a.documents d WITH d = :DocumentID
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT a
			FROM VidalMainBundle:ATC a
			JOIN a.products p
			WHERE p IN (:productIds)
		')->setParameter('productIds', $productIds)
			->getResult();
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('DISTINCT a.ATCCode, a.RusName, a.EngName, a.ParentATCCode')
			->from('VidalMainBundle:ATC', 'a')
			->where('a.ATCCode LIKE :q')
			->orderBy('a.ATCCode', 'ASC')
			->setParameter('q', $q . '%');

		# поиск по словам
		$words = explode(' ', $q);
		$where = '';

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' OR ';
			}
			$where .= "(a.RusName LIKE '$word%' OR a.EngName LIKE '$word%' OR a.RusName LIKE '% $word%' OR a.EngName LIKE '% $word%')";
		}

		$qb->orWhere($where);

		$atcCodesRaw = $qb->getQuery()->getResult();
		$atcCodes    = array();

		for ($i = 0, $c = count($atcCodesRaw); $i < $c; $i++) {
			$key            = $atcCodesRaw[$i]['ATCCode'];
			$atcCodes[$key] = $atcCodesRaw[$i];
		}

		return $atcCodes;
	}

	public function findAll()
	{
		$atcRaw = $this->_em->createQuery('
			SELECT a.ATCCode, a.RusName, a.EngName, a.ParentATCCode
			FROM VidalMainBundle:ATC a
			ORDER BY a.ATCCode ASC
		')->getResult();

		$atc = array();

		for ($i = 0; $i < count($atcRaw); $i++) {
			$key               = $atcRaw[$i]['ATCCode'];
			$atc[$key]         = $atcRaw[$i];
			$atc[$key]['list'] = array();
		}

		return $atc;
	}
}