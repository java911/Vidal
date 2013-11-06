<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="document_infopage") */
class DocumentInfoPage
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Document", inversedBy="documentInfoPages")
	 * @ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")
	 */
	protected $DocumentID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="InfoPage", inversedBy="documentInfoPages")
	 * @ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")
	 */
	protected $InfoPageID;

	/** @ORM\Column(type="integer", nullable=true) */
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