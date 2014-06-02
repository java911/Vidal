<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="product_package") */
class ProductPackage
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $ProductPackageID;

	/**
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="productPackages")
	 * @ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")
	 */
	protected $ProductID;

	/**
	 * @ORM\ManyToOne(targetEntity="Package", inversedBy="productPackages")
	 * @ORM\JoinColumn(name="PackageID", referencedColumnName="PackageID")
	 */
	protected $PackageID;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Notes;

	/** @ORM\Column(length=50, nullable=true) */
	protected $BarCode;

	/**
	 * @ORM\ManyToOne(targetEntity="Picture", inversedBy="productPackages")
	 * @ORM\JoinColumn(name="PictureID", referencedColumnName="PictureID")
	 */
	protected $PictureID;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ItemPerBox;

	/** @ORM\Column(length=50, nullable=true) */
	protected $BoxQuantityUnit;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $r;

	/** @ORM\Column(length=50, nullable=true) */
	protected $UniqNx;

	/**
	 * @param mixed $BarCode
	 */
	public function setBarCode($BarCode)
	{
		$this->BarCode = $BarCode;
	}

	/**
	 * @return mixed
	 */
	public function getBarCode()
	{
		return $this->BarCode;
	}

	/**
	 * @param mixed $BoxQuantityUnit
	 */
	public function setBoxQuantityUnit($BoxQuantityUnit)
	{
		$this->BoxQuantityUnit = $BoxQuantityUnit;
	}

	/**
	 * @return mixed
	 */
	public function getBoxQuantityUnit()
	{
		return $this->BoxQuantityUnit;
	}

	/**
	 * @param mixed $ItemPerBox
	 */
	public function setItemPerBox($ItemPerBox)
	{
		$this->ItemPerBox = $ItemPerBox;
	}

	/**
	 * @return mixed
	 */
	public function getItemPerBox()
	{
		return $this->ItemPerBox;
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
	 * @param mixed $PictureID
	 */
	public function setPictureID($PictureID)
	{
		$this->PictureID = $PictureID;
	}

	/**
	 * @return mixed
	 */
	public function getPictureID()
	{
		return $this->PictureID;
	}

	/**
	 * @param mixed $ProductID
	 */
	public function setProductID($ProductID)
	{
		$this->ProductID = $ProductID;
	}

	/**
	 * @return mixed
	 */
	public function getProductID()
	{
		return $this->ProductID;
	}

	/**
	 * @param mixed $ProductPackageID
	 */
	public function setProductPackageID($ProductPackageID)
	{
		$this->ProductPackageID = $ProductPackageID;
	}

	/**
	 * @return mixed
	 */
	public function getProductPackageID()
	{
		return $this->ProductPackageID;
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
	 * @param mixed $UniqNx
	 */
	public function setUniqNx($UniqNx)
	{
		$this->UniqNx = $UniqNx;
	}

	/**
	 * @return mixed
	 */
	public function getUniqNx()
	{
		return $this->UniqNx;
	}

	/**
	 * @param mixed $r
	 */
	public function setR($r)
	{
		$this->r = $r;
	}

	/**
	 * @return mixed
	 */
	public function getR()
	{
		return $this->r;
	}
}