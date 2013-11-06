<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="unit") */
class Unit
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $UnitID;

	/** @ORM\Column(length=40) */
	protected $RusName;

	/** @ORM\Column(length=40, nullable=true) */
	protected $EngName;

	/** @ORM\OneToMany(targetEntity="Package", mappedBy="ItemQuantityUnitID") */
	protected $packages;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $GDDB_UnitID;

	public function __construct()
	{
		$this->packages = new ArrayCollection();
	}

	public function __string()
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
	 * @param mixed $GDDB_UnitID
	 */
	public function setGDDBUnitID($GDDB_UnitID)
	{
		$this->GDDB_UnitID = $GDDB_UnitID;
	}

	/**
	 * @return mixed
	 */
	public function getGDDBUnitID()
	{
		return $this->GDDB_UnitID;
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
	 * @param mixed $packages
	 */
	public function setPackages(ArrayCollection $packages)
	{
		$this->packages = $packages;
	}

	/**
	 * @return mixed
	 */
	public function getPackages()
	{
		return $this->packages;
	}
}