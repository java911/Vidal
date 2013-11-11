<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="item") */
class Item
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $ItemID;

	/** @ORM\Column(type="text") */
	protected $RusName;

	/**
	 * @ORM\ManyToOne(targetEntity="Form", inversedBy="items")
	 * @ORM\JoinColumn(name="FormID", referencedColumnName="FormID")
	 */
	protected $FormID;

	/** @ORM\Column(length=1500, nullable=true) */
	protected $DescriptionForm;

	/**
	 * @ORM\ManyToOne(targetEntity="Picture", inversedBy="items")
	 * @ORM\JoinColumn(name="PictureID", referencedColumnName="PictureID")
	 */
	protected $PictureID;

	/** @ORM\OneToMany(targetEntity="ItemInactiveIng", mappedBy="ItemID") */
	protected $itemInactiveIngs;

	public function __construct()
	{
		$this->itemInactiveIngs = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
	}

	/**
	 * @param mixed $DescriptionForm
	 */
	public function setDescriptionForm($DescriptionForm)
	{
		$this->DescriptionForm = $DescriptionForm;
	}

	/**
	 * @return mixed
	 */
	public function getDescriptionForm()
	{
		return $this->DescriptionForm;
	}

	/**
	 * @param mixed $FormID
	 */
	public function setFormID($FormID)
	{
		$this->FormID = $FormID;
	}

	/**
	 * @return mixed
	 */
	public function getFormID()
	{
		return $this->FormID;
	}

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
	 * @param mixed $itemInactiveIngs
	 */
	public function setItemInactiveIngs(ArrayCollection $itemInactiveIngs)
	{
		$this->itemInactiveIngs = $itemInactiveIngs;
	}

	/**
	 * @return mixed
	 */
	public function getItemInactiveIngs()
	{
		return $this->itemInactiveIngs;
	}
}