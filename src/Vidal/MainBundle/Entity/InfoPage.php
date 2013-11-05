<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/** @ORM\Entity @ORM\Table(name="infopage") */
class InfoPage
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $InfoPageID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\Column(type="text", nullable=true) */
	protected $RusAddress;

	/** @ORM\Column(type="text", nullable=true) */
	protected $EngAddress;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ShortAddress;

	/**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="infoPages")
	 * @ORM\JoinColumn(name="CountryCode", referencedColumnName="CountryCode")
	 */
	protected $CoutryCode;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Notes;

	/** @ORM\Column(length=100, nullable=true) */
	protected $PhoneNumber;

	/** @ORM\Column(length=100, nullable=true) */
	protected $Fax;

	/** @ORM\Column(length=100, nullable=true) */
	protected $Email;

	/** @ORM\Column(type="boolean") */
	protected $WithoutPage = false;

	/** @ORM\Column(type="datetime") @Gedmo\Timestampable(on="update") */
	protected $DateTextModified;

	/**
	 * @ORM\ManyToOne(targetEntity="CountryEdition", inversedBy="infoPages")
	 * @ORM\JoinColumn(name="CountryEditionCode", referencedColumnName="CountryEditionCode")
	 */
	protected $CoutryEditionCode;

	public function __construct()
	{

	}

	public function __toString()
	{
		return $this->RusName;
	}
}