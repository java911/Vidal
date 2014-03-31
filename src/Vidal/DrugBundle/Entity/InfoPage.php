<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/** @ORM\Entity(repositoryClass="InfoPageRepository") @ORM\Table(name="infopage") */
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
	protected $CountryCode;

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
	 * @ORM\Column(length=10)
	 */
	protected $CountryEditionCode;

	/** @ORM\ManyToMany(targetEntity="Picture", mappedBy="infoPages") */
	protected $pictures;

	/** @ORM\OneToMany(targetEntity="DocumentInfoPage", mappedBy="InfoPageID") */
	protected $documentInfoPages;

	/**
	 * @ORM\ManyToMany(targetEntity="RepresAndDistribs", mappedBy="infoPages")
	 * @ORM\JoinTable(name="infopage_represanddistribs",
	 *        joinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="RepresAndDistribID", referencedColumnName="RepresAndDistribID")})
	 */
	protected $represAndDistribs;

	/**
	 * @ORM\ManyToMany(targetEntity="Article", mappedBy="infoPages")
	 * @ORM\JoinTable(name="article_infopage",
	 *        joinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")})
	 */
	protected $articles;

	/**
	 * @ORM\ManyToMany(targetEntity="Art", mappedBy="infoPages")
	 * @ORM\JoinTable(name="art_infopage",
	 *        joinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")})
	 */
	protected $arts;

	public function __construct()
	{
		$this->pictures          = new ArrayCollection();
		$this->documentInfoPages = new ArrayCollection();
		$this->articles          = new ArrayCollection();
		$this->arts              = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->InfoPageID . ' - ' . $this->RusName;
	}

	/**
	 * @param mixed $DateTextModified
	 */

	public function setDateTextModified($DateTextModified)
	{
		$this->DateTextModified = $DateTextModified;
	}

	/**
	 * @return mixed
	 */
	public function getDateTextModified()
	{
		return $this->DateTextModified;
	}

	/**
	 * @param mixed $Email
	 */
	public function setEmail($Email)
	{
		$this->Email = $Email;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->Email;
	}

	/**
	 * @param mixed $EngAddress
	 */
	public function setEngAddress($EngAddress)
	{
		$this->EngAddress = $EngAddress;
	}

	/**
	 * @return mixed
	 */
	public function getEngAddress()
	{
		return $this->EngAddress;
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
	 * @param mixed $Fax
	 */
	public function setFax($Fax)
	{
		$this->Fax = $Fax;
	}

	/**
	 * @return mixed
	 */
	public function getFax()
	{
		return $this->Fax;
	}

	/**
	 * @param mixed $InfoPageID
	 */
	public function setInfoPageID($InfoPageID)
	{
		$this->InfoPageID = $InfoPageID;
	}

	/**
	 * @return mixed
	 */
	public function getInfoPageID()
	{
		return $this->InfoPageID;
	}

	/**
	 * @param mixed $Notes
	 */
	public function setNotes($Notes)
	{
		$this->Notes = $Notes;
	}

	/**
	 * @return mixed
	 */
	public function getNotes()
	{
		return $this->Notes;
	}

	/**
	 * @param mixed $PhoneNumber
	 */
	public function setPhoneNumber($PhoneNumber)
	{
		$this->PhoneNumber = $PhoneNumber;
	}

	/**
	 * @return mixed
	 */
	public function getPhoneNumber()
	{
		return $this->PhoneNumber;
	}

	/**
	 * @param mixed $RusAddress
	 */
	public function setRusAddress($RusAddress)
	{
		$this->RusAddress = $RusAddress;
	}

	/**
	 * @return mixed
	 */
	public function getRusAddress()
	{
		return $this->RusAddress;
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
	 * @param mixed $ShortAddress
	 */
	public function setShortAddress($ShortAddress)
	{
		$this->ShortAddress = $ShortAddress;
	}

	/**
	 * @return mixed
	 */
	public function getShortAddress()
	{
		return $this->ShortAddress;
	}

	/**
	 * @param mixed $WithoutPage
	 */
	public function setWithoutPage($WithoutPage)
	{
		$this->WithoutPage = $WithoutPage;
	}

	/**
	 * @return mixed
	 */
	public function getWithoutPage()
	{
		return $this->WithoutPage;
	}

	/**
	 * @param mixed $pictures
	 */
	public function setPictures(ArrayCollection $pictures)
	{
		$this->pictures = $pictures;
	}

	/**
	 * @return mixed
	 */
	public function getPictures()
	{
		return $this->pictures;
	}

	/**
	 * @param mixed $documentInfoPages
	 */
	public function setDocumentInfoPages(ArrayCollection $documentInfoPages)
	{
		$this->documentInfoPages = $documentInfoPages;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentInfoPages()
	{
		return $this->documentInfoPages;
	}

	/**
	 * @param mixed $CountryCode
	 */
	public function setCountryCode($CountryCode)
	{
		$this->CountryCode = $CountryCode;
	}

	/**
	 * @return mixed
	 */
	public function getCountryCode()
	{
		return $this->CountryCode;
	}

	/**
	 * @param mixed $articles
	 */
	public function setArticles($articles)
	{
		$this->articles = $articles;
	}

	/**
	 * @return mixed
	 */
	public function getArticles()
	{
		return $this->articles;
	}

	/**
	 * @param mixed $represAndDistribs
	 */
	public function setRepresAndDistribs($represAndDistribs)
	{
		$this->represAndDistribs = $represAndDistribs;
	}

	/**
	 * @return mixed
	 */
	public function getRepresAndDistribs()
	{
		return $this->represAndDistribs;
	}

	/**
	 * @param mixed $CountryEditionCode
	 */
	public function setCountryEditionCode($CountryEditionCode)
	{
		$this->CountryEditionCode = $CountryEditionCode;
	}

	/**
	 * @return mixed
	 */
	public function getCountryEditionCode()
	{
		return $this->CountryEditionCode;
	}

	/**
	 * @param mixed $arts
	 */
	public function setArts($arts)
	{
		$this->arts = $arts;
	}

	/**
	 * @return mixed
	 */
	public function getArts()
	{
		return $this->arts;
	}
}