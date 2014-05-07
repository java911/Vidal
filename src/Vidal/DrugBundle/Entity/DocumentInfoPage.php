<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="document_infopage") */
class DocumentInfoPage
{
	/** @ORM\Column(type="integer") @ORM\Id */
	protected $DocumentID;

	/** @ORM\Column(type="integer") @ORM\Id */
	protected $InfoPageID;

	/** @ORM\Column(type="integer") */
	protected $Ranking;

	/**
	 * @param mixed $DocumentID
	 */
	public function setDocumentID($DocumentID)
	{
		$this->DocumentID = $DocumentID;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentID()
	{
		return $this->DocumentID;
	}

	/**
	 * @param mixed $InfoPageID
	 */
	public function setInfoPageID($InfoPageID)
	{
		$this->InfoPageID = $InfoPageID;
	}

	/**
	 * @return mixed
	 */
	public function getInfoPageID()
	{
		return $this->InfoPageID;
	}

	/**
	 * @param mixed $Ranking
	 */
	public function setRanking($Ranking)
	{
		$this->Ranking = $Ranking;
	}

	/**
	 * @return mixed
	 */
	public function getRanking()
	{
		return $this->Ranking;
	}
}