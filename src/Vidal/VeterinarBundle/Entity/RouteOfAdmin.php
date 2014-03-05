<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="route_of_admin") */
class RouteOfAdmin
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $RouteID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $FoodDSMRouteID;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $HealthDSMRouteID;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $DrugDSMRouteID;

	/** @ORM\OneToMany(targetEntity="ProductItemRoute", mappedBy="RouteID") */
	protected $productItemRoutes;

	public function __construct()
	{
		$this->productItemRoutes = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
	}

	/**
	 * @param mixed $DrugDSMRouteID
	 */
	public function setDrugDSMRouteID($DrugDSMRouteID)
	{
		$this->DrugDSMRouteID = $DrugDSMRouteID;
	}

	/**
	 * @return mixed
	 */
	public function getDrugDSMRouteID()
	{
		return $this->DrugDSMRouteID;
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
	 * @param mixed $FoodDSMRouteID
	 */
	public function setFoodDSMRouteID($FoodDSMRouteID)
	{
		$this->FoodDSMRouteID = $FoodDSMRouteID;
	}

	/**
	 * @return mixed
	 */
	public function getFoodDSMRouteID()
	{
		return $this->FoodDSMRouteID;
	}

	/**
	 * @param mixed $HealthDSMRouteID
	 */
	public function setHealthDSMRouteID($HealthDSMRouteID)
	{
		$this->HealthDSMRouteID = $HealthDSMRouteID;
	}

	/**
	 * @return mixed
	 */
	public function getHealthDSMRouteID()
	{
		return $this->HealthDSMRouteID;
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
	 * @param mixed $productItemRoutes
	 */
	public function setProductItemRoutes(ArrayCollection $productItemRoutes)
	{
		$this->productItemRoutes = $productItemRoutes;
	}

	/**
	 * @return mixed
	 */
	public function getProductItemRoutes()
	{
		return $this->productItemRoutes;
	}
}