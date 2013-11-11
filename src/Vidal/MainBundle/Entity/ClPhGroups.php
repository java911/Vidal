<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="clphgroups") */
class ClPhGroups
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $ClPhGroupsID;

	/** @ORM\Column(length=255) */
	protected $Name;

	/** @ORM\Column(length=50, nullable=true) */
	protected $Code;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", mappedBy="clphGroups")
	 * @ORM\JoinTable(name="product_clphgroups",
	 *        joinColumns={@ORM\JoinColumn(name="ClPhGroupsID", referencedColumnName="ClPhGroupsID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
	 */
	protected $products;

	public function __construct()
	{
		$this->products = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->Name;
	}


}