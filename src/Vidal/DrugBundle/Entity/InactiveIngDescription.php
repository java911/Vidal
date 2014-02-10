<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="inactiveingdescription") */
class InactiveIngDescription
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $DescriptionID;

	/** @ORM\Column(type="text") */
	protected $Description;

	/** @ORM\OneToMany(targetEntity="ItemInactiveIng", mappedBy="DescriptionID") */
	protected $itemInactiveIngs;

	public function __construct()
	{
		$this->itemInactiveIngs = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->Description;
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

	public function setItemInactiveIngs(ArrayCollection $itemInactiveIngs)
	{
		$this->itemInactiveIngs = $itemInactiveIngs;
	}

	public function getItemInactiveIngs()
	{
		return $this->itemInactiveIngs;
	}
}