<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="product_item") */
class ProductItem
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="productItems")
	 * @ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")
	 */
	protected $ProductID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Item", inversedBy="productItems")
	 * @ORM\JoinColumn(name="ItemID", referencedColumnName="ItemID")
	 */
	protected $ItemID;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;

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
}