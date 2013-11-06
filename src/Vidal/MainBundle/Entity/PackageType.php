<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="packageItem") */
class PackageItem
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $PackageItemID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $GDDB_PackageItemID;
}