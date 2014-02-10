<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="country") */
class Country
{
	/** @ORM\Id @ORM\Column(length=10) */
	protected $CountryCode;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $EngName;

	/** @ORM\OneToMany(targetEntity="Company", mappedBy="CountryCode") */
	protected $companies;

	/** @ORM\OneToMany(targetEntity="InfoPage", mappedBy="CountryCode") */
	protected $infoPages;

	/** @ORM\OneToMany(targetEntity="RepresAndDistribs", mappedBy="CountryCode") */
	protected $represAndDistribs;

	public function __construct()
	{
		$this->companies         = new ArrayCollection();
		$this->infoPages         = new ArrayCollection();
		$this->represAndDistribs = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
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

	/**
	 * @param mixed $infoPages
	 */
	public function setInfoPages(ArrayCollection $infoPages)
	{
		$this->infoPages = $infoPages;
	}

	/**
	 * @return mixed
	 */
	public function getInfoPages()
	{
		return $this->infoPages;
	}

	/**
	 * @param mixed $represAndDistribs
	 */
	public function setRepresAndDistribs(ArrayCollection $represAndDistribs)
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
}