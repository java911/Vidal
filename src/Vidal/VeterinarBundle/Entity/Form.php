<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="form") */
class Form
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $FormID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="integer", nullable=true, name="GDDB_FormID") */
	protected $gddbFormID;

	/** @ORM\Column(length=255, nullable=true) */
	protected $ShortName;

	/** @ORM\OneToMany(targetEntity="Item", mappedBy="FormID") */
	protected $items;

	public function __construct()
	{
		$this->items = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
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
	 * @param mixed $gddbFormID
	 */
	public function setGddbFormID($gddbFormID)
	{
		$this->gddbFormID = $gddbFormID;
	}

	/**
	 * @return mixed
	 */
	public function getGddbFormID()
	{
		return $this->gddbFormID;
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
	 * @param mixed $ShortName
	 */
	public function setShortName($ShortName)
	{
		$this->ShortName = $ShortName;
	}

	/**
	 * @return mixed
	 */
	public function getShortName()
	{
		return $this->ShortName;
	}

	/**
	 * @param mixed $items
	 */
	public function setItems(ArrayCollection $items)
	{
		$this->items = $items;
	}

	/**
	 * @return mixed
	 */
	public function getItems()
	{
		return $this->items;
	}
}