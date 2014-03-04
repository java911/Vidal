<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="package") */
class Package
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $PackageID;

	/** @ORM\Column(type="text") */
	protected $PackageName;

	/** @ORM\Column(length=50, nullable=true) */
	protected $ItemQuantity;

	/**
	 * @ORM\ManyToOne(targetEntity="Unit", inversedBy="packages")
	 * @ORM\JoinColumn(name="ItemQuantityUnitID", referencedColumnName="UnitID")
	 */
	protected $ItemQuantityUnitID;

	/** @ORM\Column(length=50, nullable=true) */
	protected $ItemQuantityNotes;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ItemPerPack;

	/** @ORM\OneToMany(targetEntity="ProductPackage", mappedBy="PackageID") */
	protected $productPackages;

	/** @ORM\OneToMany(targetEntity="PackagePackageItem", mappedBy="PackageID") */
	protected $packagePackageItems;

	public function __construct()
	{
		$this->productPackages     = new ArrayCollection();
		$this->packagePackageItems = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->PackageName;
	}

	/**
	 * @param mixed $ItemPerPack
	 */
	public function setItemPerPack($ItemPerPack)
	{
		$this->ItemPerPack = $ItemPerPack;
	}

	/**
	 * @return mixed
	 */
	public function getItemPerPack()
	{
		return $this->ItemPerPack;
	}

	/**
	 * @param mixed $ItemQuantity
	 */
	public function setItemQuantity($ItemQuantity)
	{
		$this->ItemQuantity = $ItemQuantity;
	}

	/**
	 * @return mixed
	 */
	public function getItemQuantity()
	{
		return $this->ItemQuantity;
	}

	/**
	 * @param mixed $ItemQuantityNotes
	 */
	public function setItemQuantityNotes($ItemQuantityNotes)
	{
		$this->ItemQuantityNotes = $ItemQuantityNotes;
	}

	/**
	 * @return mixed
	 */
	public function getItemQuantityNotes()
	{
		return $this->ItemQuantityNotes;
	}

	/**
	 * @param mixed $ItemQuantityUnitID
	 */
	public function setItemQuantityUnitID($ItemQuantityUnitID)
	{
		$this->ItemQuantityUnitID = $ItemQuantityUnitID;
	}

	/**
	 * @return mixed
	 */
	public function getItemQuantityUnitID()
	{
		return $this->ItemQuantityUnitID;
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
	 * @param mixed $PackageName
	 */
	public function setPackageName($PackageName)
	{
		$this->PackageName = $PackageName;
	}

	/**
	 * @return mixed
	 */
	public function getPackageName()
	{
		return $this->PackageName;
	}

	/**
	 * @param mixed $productPackages
	 */
	public function setProductPackages(ArrayCollection $productPackages)
	{
		$this->productPackages = $productPackages;
	}

	/**
	 * @return mixed
	 */
	public function getProductPackages()
	{
		return $this->productPackages;
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