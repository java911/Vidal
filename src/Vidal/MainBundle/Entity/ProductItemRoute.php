<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="product_item_route") */
class ProductItemRoute
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="productItemRoutes")
	 * @ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")
	 */
	protected $ProductID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Item", inversedBy="productItemRoutes")
	 * @ORM\JoinColumn(name="ItemID", referencedColumnName="ItemID")
	 */
	protected $ItemID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="RouteOfAdmin", inversedBy="productItemRoutes")
	 * @ORM\JoinColumn(name="RouteID", referencedColumnName="RouteID")
	 */
	protected $RouteID;

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
	 * @param mixed $RouteID
	 */
	public function setRouteID($RouteID)
	{
		$this->RouteID = $RouteID;
	}

	/**
	 * @return mixed
	 */
	public function getRouteID()
	{
		return $this->RouteID;
	}
}