<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PictureRepository extends EntityRepository
{
	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT p.PathForElectronicEdition path
			FROM VidalDrugBundle:Picture p
			JOIN p.infoPages i
			JOIN i.documents d
			WHERE dip.DocumentID = :DocumentID AND
				ip.CountryCode = \'RUS\'
			ORDER BY dip.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findAllByProductIds($productIds, $year = null)
	{
		$year = 2015;

		if ($year) {
			$picturesRaw = $this->_em->createQuery('
				SELECT pict.PathForElectronicEdition path, prod.ProductID
				FROM VidalDrugBundle:Picture pict
				JOIN VidalDrugBundle:ProductPicture pp WITH pp.PictureID = pict.PictureID
				JOIN VidalDrugBundle:Product prod WITH pp.ProductID = prod.ProductID
				WHERE prod.ProductID IN (:productIds)
					AND pp.CountryEditionCode = \'RUS\'
					AND (pp.YearEdition = :year OR prod.ProductTypeCode = \'BAD\')
				ORDER BY prod.ProductID DESC, pp.YearEdition DESC
			')->setParameter('productIds', $productIds)
				->setParameter('year', $year)
				->getResult();
		}
		else {
			$picturesRaw = $this->_em->createQuery('
				SELECT pict.PathForElectronicEdition path, prod.ProductID
				FROM VidalDrugBundle:Picture pict
				JOIN VidalDrugBundle:ProductPicture pp WITH pp.PictureID = pict.PictureID
				JOIN VidalDrugBundle:Product prod WITH pp.ProductID = prod.ProductID
				WHERE prod.ProductID IN (:productIds)
					AND pp.CountryEditionCode = \'RUS\'
				ORDER BY prod.ProductID DESC, pp.YearEdition DESC
			')->setParameter('productIds', $productIds)
				->getResult();
		}

		$pictures = array();

		for ($i = 0; $i < count($picturesRaw); $i++) {
			$path       = preg_replace('/.+\\\\JPG\\\\/', '', $picturesRaw[$i]['path']);
			$pictures[] = $path;
		}

		return array_unique($pictures);
	}

	public function findByProductIds($productIds, $year = null)
	{
		$year = 2015;

		if ($year) {
			$picturesRaw = $this->_em->createQuery('
				SELECT pict.PathForElectronicEdition path, prod.ProductID
				FROM VidalDrugBundle:Picture pict
				JOIN VidalDrugBundle:ProductPicture pp WITH pp.PictureID = pict.PictureID
				JOIN VidalDrugBundle:Product prod WITH pp.ProductID = prod.ProductID
				WHERE prod.ProductID IN (:productIds)
					AND pp.CountryEditionCode = \'RUS\'
					AND (pp.YearEdition = :year OR prod.ProductTypeCode = \'BAD\')
				ORDER BY prod.ProductID DESC, pp.YearEdition DESC
			')->setParameter('productIds', $productIds)
				->setParameter('year', $year)
				->getResult();
		}
		else {
			$picturesRaw = $this->_em->createQuery('
				SELECT pict.PathForElectronicEdition path, prod.ProductID
				FROM VidalDrugBundle:Picture pict
				JOIN VidalDrugBundle:ProductPicture pp WITH pp.PictureID = pict.PictureID
				JOIN VidalDrugBundle:Product prod WITH pp.ProductID = prod.ProductID
				WHERE prod.ProductID IN (:productIds)
					AND pp.CountryEditionCode = \'RUS\'
				ORDER BY prod.ProductID DESC, pp.YearEdition DESC
			')->setParameter('productIds', $productIds)
				->getResult();
		}

		$pictures = array();

		for ($i = 0; $i < count($picturesRaw); $i++) {
			$key = $picturesRaw[$i]['ProductID'];
			if (!isset($pictures[$key])) {
				$path           = preg_replace('/.+\\\\JPG\\\\/', '', $picturesRaw[$i]['path']);
				$pictures[$key] = $path;
			}
		}

		$products = $this->_em->createQuery('
			SELECT p.ProductID, p.photo, p.photo2, p.photo3, p.photo4
			FROM VidalDrugBundle:Product p
			WHERE p.ProductID IN (:productIds)
				AND (p.photo IS NOT NULL OR p.photo2 IS NOT NULL OR p.photo3 IS NOT NULL OR p.photo4 IS NOT NULL)
		')->setParameter('productIds', $productIds)
			->getResult();

		foreach ($products as $product) {
			$key = $product['ProductID'];

			if ($product['photo']) {
				$pictures[$key] = $product['photo'];
			}
			elseif ($product['photo2']) {
				$pictures[$key] = $product['photo2'];
			}
			elseif ($product['photo3']) {
				$pictures[$key] = $product['photo3'];
			}
			elseif ($product['photo4']) {
				$pictures[$key] = $product['photo4'];
			}
		}

		return $pictures;
	}

	public function findByInfoPageID($InfoPageID)
	{
		$picture = $this->_em->createQuery('
			SELECT p.PathForElectronicEdition path
			FROM VidalDrugBundle:Picture p
			JOIN p.infoPages i WITH i = :InfoPageID
		')->setParameter('InfoPageID', $InfoPageID)
			->setMaxResults(1)
			->getOneOrNullResult();

		if (!empty($picture)) {
			$picture['path'] = $path = preg_replace('/.+\\\\JPG\\\\/', '', $picture['path']);
		}

		return $picture;
	}
}