<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="pervolumeunit") */
class PerVolumeUnit
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $PerVolumeUnitID;

	/** @ORM\Column(length=150) */
	protected $RusName;

	/** @ORM\Column(length=150, nullable=true) */
	protected $EngName;

	/** @ORM\Column(length=40, nullable=true) */
	protected $number;

	/** @ORM\Column(length=255, nullable=true) */
	protected $unit;

	/** @ORM\OneToMany(targetEntity="ItemActiveIng", mappedBy="PerVolumeUnitID") */
	protected $itemActiveIngs;

	public function __construct()
	{
		$this->itemActiveIngs = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
	}

	/**
	 * @param mixed $EngName
	 */
	public function setEngName($EngName)
	{
		$this->EngName = $EngName;
	}

	/**
	 * @return mixed
	 */
	public function getEngName()
	{
		return $this->EngName;
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
	 * @param mixed $RusName
	 */
	public function setRusName($RusName)
	{
		$this->RusName = $RusName;
	}

	/**
	 * @return mixed
	 */
	public function getRusName()
	{
		return $this->RusName;
	}


	/**
	 * @param mixed $itemActiveIngs
	 */
	public function setItemActiveIngs(ArrayCollection $itemActiveIngs)
	{
		$this->itemActiveIngs = $itemActiveIngs;
	}

	/**
	 * @return mixed
	 */
	public function getItemActiveIngs()
	{
		return $this->itemActiveIngs;
	}

	/**
	 * @param mixed $number
	 */
	public function setNumber($number)
	{
		$this->number = $number;
	}

	/**
	 * @return mixed
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @param mixed $unit
	 */
	public function setUnit($unit)
	{
		$this->unit = $unit;
	}

	/**
	 * @return mixed
	 */
	public function getUnit()
	{
		return $this->unit;
	}
}