<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="package_packageitem") */
class PackagePackageItem
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Package", inversedBy="packagePackageItems")
	 * @ORM\JoinColumn(name="PackageID", referencedColumnName="PackageID")
	 */
	protected $PackageID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="PackageItem", inversedBy="packagePackageItems")
	 * @ORM\JoinColumn(name="PackageItemID", referencedColumnName="PackageItemID")
	 */
	protected $PackageItemID;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Stuff;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $NumberInNextPackage;

	/**
	 * @ORM\ManyToOne(targetEntity="PackageType", inversedBy="packagePackageItems")
	 * @ORM\JoinColumn(name="PackageTypeCode", referencedColumnName="PackageTypeCode")
	 */
	protected $PackageTypeCode;

	/**
	 * @param mixed $NumberInNextPackage
	 */
	public function setNumberInNextPackage($NumberInNextPackage)
	{
		$this->NumberInNextPackage = $NumberInNextPackage;
	}

	/**
	 * @return mixed
	 */
	public function getNumberInNextPackage()
	{
		return $this->NumberInNextPackage;
	}

	/**
	 * @param mixed $PackageID
	 */
	public function setPackageID($PackageID)
	{
		$this->PackageID = $PackageID;
	}

	/**
	 * @return mixed
	 */
	public function getPackageID()
	{
		return $this->PackageID;
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
	 * @param mixed $PackageTypeCode
	 */
	public function setPackageTypeCode($PackageTypeCode)
	{
		$this->PackageTypeCode = $PackageTypeCode;
	}

	/**
	 * @return mixed
	 */
	public function getPackageTypeCode()
	{
		return $this->PackageTypeCode;
	}

	/**
	 * @param mixed $Stuff
	 */
	public function setStuff($Stuff)
	{
		$this->Stuff = $Stuff;
	}

	/**
	 * @return mixed
	 */
	public function getStuff()
	{
		return $this->Stuff;
	}


}