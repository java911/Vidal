<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="molecule_document") */
class MoleculeDocument
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Molecule", inversedBy="moleculeDocuments")
	 * @ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")
	 */
	protected $MoleculeID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Document", inversedBy="moleculeDocuments")
	 * @ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")
	 */
	protected $DocumentID;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;

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
	 * @param mixed $MoleculeID
	 */
	public function setMoleculeID($MoleculeID)
	{
		$this->MoleculeID = $MoleculeID;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeID()
	{
		return $this->MoleculeID;
	}
}