<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="packagetype") */
class PackageType
{
	/** @ORM\Id @ORM\Column(length=4, unique=true) */
	protected $PackageTypeCode;

	/** @ORM\Column(type="smallint") */
	protected $PackageTypeIndex;

	/** @ORM\OneToMany(targetEntity="PackagePackageItem", mappedBy="PackageTypeCode") */
	protected $packagePackageItem;

	public function __construct()
	{
		$this->packagePackageItem = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->PackageTypeCode;
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
	 * @param mixed $PackageTypeIndex
	 */
	public function setPackageTypeIndex($PackageTypeIndex)
	{
		$this->PackageTypeIndex = $PackageTypeIndex;
	}

	/**
	 * @return mixed
	 */
	public function getPackageTypeIndex()
	{
		return $this->PackageTypeIndex;
	}

	/**
	 * @param mixed $packagePackageItem
	 */
	public function setPackagePackageItem(ArrayCollection $packagePackageItem)
	{
		$this->packagePackageItem = $packagePackageItem;
	}

	/**
	 * @return mixed
	 */
	public function getPackagePackageItem()
	{
		return $this->packagePackageItem;
	}
}