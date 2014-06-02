<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="companygroup") */
class CompanyGroup
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $CompanyGroupID;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255) */
	protected $EngName;

	/**
	 * @ORM\ManyToMany(targetEntity="Company", inversedBy="companyGroups")
	 * @ORM\JoinTable(name="company_companygroup",
	 *        joinColumns={@ORM\JoinColumn(name="CompanyGroupID", referencedColumnName="CompanyGroupID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="CompanyID", referencedColumnName="CompanyID")})
	 */
	protected $companies;

	public function __construct()
	{
		$this->companies = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->EngName;
	}

	/**
	 * @param mixed $CompanyGroupID
	 */
	public function setCompanyGroupID($CompanyGroupID)
	{
		$this->CompanyGroupID = $CompanyGroupID;
	}

	/**
	 * @return mixed
	 */
	public function getCompanyGroupID()
	{
		return $this->CompanyGroupID;
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
	 * @param mixed $companies
	 */
	public function setCompanies(ArrayCollection $companies)
	{
		$this->companies = $companies;
	}

	/**
	 * @return mixed
	 */
	public function getCompanies()
	{
		return $this->companies;
	}
}