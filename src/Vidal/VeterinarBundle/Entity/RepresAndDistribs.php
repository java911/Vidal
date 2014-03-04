<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="represanddistribs") */
class RepresAndDistribs
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $RepresAndDistribID;

	/** @ORM\Column(length=255) */
	protected $InfoPageName;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $CommonName;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Address;

	/**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="represAndDistribs")
	 * @ORM\JoinColumn(name="CountryCode", referencedColumnName="CountryCode")
	 */
	protected $CountryCode;

	/**
	 * @ORM\ManyToMany(targetEntity="InfoPage", inversedBy="represAndDistribs")
	 * @ORM\JoinTable(name="infopage_represanddistribs",
	 *        joinColumns={@ORM\JoinColumn(name="RepresAndDistribID", referencedColumnName="RepresAndDistribID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")})
	 */
	protected $infoPages;
}