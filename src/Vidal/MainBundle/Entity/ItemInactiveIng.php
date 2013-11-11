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
}