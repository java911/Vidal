<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="countryedition") */
class CountryEdition
{
	/** @ORM\Id @ORM\Column(length=10, unique=true) */
	protected $CountryEditionCode;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\OneToMany(targetEntity="Company", mappedBy="CountryEditionCode") */
	protected $companies;

	/** @ORM\OneToMany(targetEntity="DocumentEdition", mappedBy="CountryEditionCode") */
	protected $documentEditions;

	public function __construct()
	{
		$this->companies        = new ArrayCollection();
		$this->documentEditions = new ArrayCollection;
	}

	public function __toString()
	{
		return empty($this->RusName) ? '' : $this->RusName;
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
	 * @param mixed $documentEditions
	 */
	public function setDocumentEditions(ArrayCollection $documentEditions)
	{
		$this->documentEditions = $documentEditions;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentEditions()
	{
		return $this->documentEditions;
	}
}