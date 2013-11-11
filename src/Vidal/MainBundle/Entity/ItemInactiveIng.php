<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="item_inactiveing") */
class ItemInactiveIng
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Item", inversedBy="itemInactiveIngs")
	 * @ORM\JoinColumn(name="ItemID", referencedColumnName="ItemID")
	 */
	protected $ItemID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="InactiveIng", inversedBy="itemInactiveIngs")
	 * @ORM\JoinColumn(name="InactiveIngredientID", referencedColumnName="InactiveIngredientID")
	 */
	protected $InactiveIngredientID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="InactiveIngDescription", inversedBy="descriptions")
	 * @ORM\JoinColumn(name="DescriptionID", referencedColumnName="DescriptionID")
	 */
	protected $DescriptionID;

	/** @ORM\Column(length=255, nullable=true) */
	protected $Volume;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;

	/**
	 * @param mixed $DescriptionID
	 */
	public function setDescriptionID($DescriptionID)
	{
		$this->DescriptionID = $DescriptionID;
	}

	/**
	 * @return mixed
	 */
	public function getDescriptionID()
	{
		return $this->DescriptionID;
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