<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="inactiveing") */
class InactiveIng
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $InactiveIngredientID;

	/** @ORM\Column(length=1000, nullable=true) */
	protected $RusName;

	/** @ORM\Column(length=1000, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ParentIngredientID;

	/** @ORM\Column(type="text") */
	protected $Description;

	/** @ORM\OneToMany(targetEntity="ItemInactiveIng", mappedBy="InactiveIngredientID") */
	protected $itemInactiveIngs;

	/**
	 * @ORM\ManyToMany(targetEntity="Item", inversedBy="ingredientsNotIncluded")
     * @ORM\JoinTable(name="item_ingredient_not_included",
     * 		joinColumns={@ORM\JoinColumn(name="IngredientID", referencedColumnName="InactiveIngredientID")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="ItemID", referencedColumnName="ItemID")})
	 */
	protected $itemsNotIncluded;

	public function __construct()
	{
		$this->itemInactiveIngs = new ArrayCollection();
		$this->itemsNotIncluded = new ArrayCollection();
	}

	public function __toString()
	{
		return empty($this->RusName) ? $this->EngName : $this->RusName;
	}

	/**
	 * @param mixed $Description
	 */
	public function setDescription($Description)
	{
		$this->Description = $Description;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->Description;
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
	 * @param mixed $InactiveIngredientID
	 */
	public function setInactiveIngredientID($InactiveIngredientID)
	{
		$this->InactiveIngredientID = $InactiveIngredientID;
	}

	/**
	 * @return mixed
	 */
	public function getInactiveIngredientID()
	{
		return $this->InactiveIngredientID;
	}

	/**
	 * @param mixed $ParentIngredientID
	 */
	public function setParentIngredientID($ParentIngredientID)
	{
		$this->ParentIngredientID = $ParentIngredientID;
	}

	/**
	 * @return mixed
	 */
	public function getParentIngredientID()
	{
		return $this->ParentIngredientID;
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
	public function setItemInactiveIngs($itemInactiveIngs)
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

	/**
	 * @param mixed $itemsNotIncluded
	 */
	public function setItemsNotIncluded($itemsNotIncluded)
	{
		$this->itemsNotIncluded = $itemsNotIncluded;
	}

	/**
	 * @return mixed
	 */
	public function getItemsNotIncluded()
	{
		return $this->itemsNotIncluded;
	}


}