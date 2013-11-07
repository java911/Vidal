<?php
namespace Vidal\MainBundle\Entity;

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
	protected $GDDB_MOLECULENAMEID;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $GDDB_MoleculeID;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ParentIngredientID;

	/** @ORM\Column(type="text") */
	protected $Description;

	/** @ORM\OneToMany(targetEntity="ItemInactiveIng", mappedBy="InactiveIngredientID") */
	protected $itemInactiveIngs;
}