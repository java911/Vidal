<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="item_activeing") */
class ItemActiveIng
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Item", inversedBy="itemActiveIngs")
	 * @ORM\JoinColumn(name="ItemID", referencedColumnName="ItemID")
	 */
	protected $ItemID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="MoleculeName", inversedBy="itemActiveIngs")
	 * @ORM\JoinColumn(name="MoleculeNameID", referencedColumnName="MoleculeNameID")
	 */
	protected $MoleculeNameID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="PerVolumeUnit", inversedBy="itemActiveIngs")
	 * @ORM\JoinColumn(name="PerVolumeUnitID", referencedColumnName="PerVolumeUnitID")
	 */
	protected $PerVolumeUnitID;

	/**
	 * @ORM\ManyToOne(targetEntity="Unit", inversedBy="itemActiveIngs")
	 * @ORM\JoinColumn(name="UnitID", referencedColumnName="UnitID")
	 */
	protected $UnitID;

	/** @ORM\Column(length=50, nullable=true) */
	protected $Volume;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Notes;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $RankingByPVU;

	/**
	 * @param mixed $ItemID
	 */
	public function setItemID($ItemID)
	{
		$this->ItemID = $ItemID;
	}

	/**
	 * @return mixed
	 */
	public function getItemID()
	{
		return $this->ItemID;
	}

	/**
	 * @param mixed $MoleculeNameID
	 */
	public function setMoleculeNameID($MoleculeNameID)
	{
		$this->MoleculeNameID = $MoleculeNameID;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeNameID()
	{
		return $this->MoleculeNameID;
	}

	/**
	 * @param mixed $Notes
	 */
	public function setNotes($Notes)
	{
		$this->Notes = $Notes;
	}

	/**
	 * @return mixed
	 */
	public function getNotes()
	{
		return $this->Notes;
	}

	/**
	 * @param mixed $PerVolumeUnitID
	 */
	public function setPerVolumeUnitID($PerVolumeUnitID)
	{
		$this->PerVolumeUnitID = $PerVolumeUnitID;
	}

	/**
	 * @return mixed
	 */
	public function getPerVolumeUnitID()
	{
		return $this->PerVolumeUnitID;
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

	/**
	 * @param mixed $RankingByPVU
	 */
	public function setRankingByPVU($RankingByPVU)
	{
		$this->RankingByPVU = $RankingByPVU;
	}

	/**
	 * @return mixed
	 */
	public function getRankingByPVU()
	{
		return $this->RankingByPVU;
	}

	/**
	 * @param mixed $UnitID
	 */
	public function setUnitID($UnitID)
	{
		$this->UnitID = $UnitID;
	}

	/**
	 * @return mixed
	 */
	public function getUnitID()
	{
		return $this->UnitID;
	}

	/**
	 * @param mixed $Volume
	 */
	public function setVolume($Volume)
	{
		$this->Volume = $Volume;
	}

	/**
	 * @return mixed
	 */
	public function getVolume()
	{
		return $this->Volume;
	}
}