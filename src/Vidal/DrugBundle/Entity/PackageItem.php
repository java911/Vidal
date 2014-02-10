<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="packageitem") */
class PackageItem
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $PackageItemID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $GDDB_PackageItemID;

	/** @ORM\OneToMany(targetEntity="PackagePackageItem", mappedBy="PackageItemID") */
	protected $packagePackageItems;

	public function __construct()
	{
		$this->packagePackageItems = new ArrayCollection();
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
	 * @param mixed $GDDB_PackageItemID
	 */
	public function setGDDBPackageItemID($GDDB_PackageItemID)
	{
		$this->GDDB_PackageItemID = $GDDB_PackageItemID;
	}

	/**
	 * @return mixed
	 */
	public function getGDDBPackageItemID()
	{
		return $this->GDDB_PackageItemID;
	}

	/**
	 * @param mixed $PackageItemID
	 */
	public function setPackageItemID($PackageItemID)
	{
		$this->PackageItemID = $PackageItemID;
	}

	/**
	 * @return mixed
	 */
	public function getPackageItemID()
	{
		return $this->PackageItemID;
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
	 * @param mixed $packagePackageItems
	 */
	public function setPackagePackageItems(ArrayCollection $packagePackageItems)
	{
		$this->packagePackageItems = $packagePackageItems;
	}

	/**
	 * @return mixed
	 */
	public function getPackagePackageItems()
	{
		return $this->packagePackageItems;
	}
}