<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="company") */
class Company
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $CompanyID;

	/** @ORM\Column(length=255) */
	protected $LocalName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $GDDBName;

	/** @ORM\Column(length=30, nullable=true) */
	protected $Property;

	/**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="countries")
	 * @ORM\JoinColumn(name="CountryCode", referencedColumnName="CountryCode")
	 */
	protected $CountryCode;

	/**
	 * @ORM\ManyToOne(targetEntity="CountryEdition", inversedBy="companies")
	 * @ORM\JoinColumn(name="CountryEditionCode", referencedColumnName="CountryEditionCode")
	 */
	protected $CountryEditionCode;

	/**
	 * @ORM\ManyToMany(targetEntity="CompanyGroup", mappedBy="companies")
	 * @ORM\JoinTable(name="company_companygroup",
	 *        joinColumns={@ORM\JoinColumn(name="CompanyID", referencedColumnName="CompanyID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="CompanyGroupID", referencedColumnName="CompanyGroupID")})
	 */
	protected $companyGroups;

	/** @ORM\OneToMany(targetEntity="ProductCompany", mappedBy="CompanyID") */
	protected $productCompany;

	public function __construct()
	{
		$companyGroups    = new ArrayCollection();
		$productCompanies = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->LocalName;
	}

	/**
	 * @param mixed $CompanyID
	 */
	public function setCompanyID($CompanyID)
	{
		$this->CompanyID = $CompanyID;
	}

	/**
	 * @return mixed
	 */
	public function getCompanyID()
	{
		return $this->CompanyID;
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
	 * @param mixed $GDDBName
	 */
	public function setGDDBName($GDDBName)
	{
		$this->GDDBName = $GDDBName;
	}

	/**
	 * @return mixed
	 */
	public function getGDDBName()
	{
		return $this->GDDBName;
	}

	/**
	 * @param mixed $LocalName
	 */
	public function setLocalName($LocalName)
	{
		$this->LocalName = $LocalName;
	}

	/**
	 * @return mixed
	 */
	public function getLocalName()
	{
		return $this->LocalName;
	}

	/**
	 * @param mixed $Property
	 */
	public function setProperty($Property)
	{
		$this->Property = $Property;
	}

	/**
	 * @return mixed
	 */
	public function getProperty()
	{
		return $this->Property;
	}

	/**
	 * @param mixed $companyGroups
	 */
	public function setCompanyGroups(ArrayCollection $companyGroups)
	{
		$this->companyGroups = $companyGroups;
	}

	/**
	 * @return mixed
	 */
	public function getCompanyGroups()
	{
		return $this->companyGroups;
	}

	/**
	 * @param mixed $productCompany
	 */
	public function setProductCompany(ArrayCollection $productCompany)
	{
		$this->productCompany = $productCompany;
	}

	/**
	 * @return mixed
	 */
	public function getProductCompany()
	{
		return $this->productCompany;
	}

}