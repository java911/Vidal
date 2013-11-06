<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="packagetype") */
class PackageType
{
	/** @ORM\Id @ORM\Column(length=4, unique=true) */
	protected $PackageTypeCode;

	/** @ORM\Column(type="smallint") */
	protected $PackageTypeIndex;

	public function __construct()
	{

	}

	public function __toString()
	{
		return $this->PackageTypeCode;
	}

}