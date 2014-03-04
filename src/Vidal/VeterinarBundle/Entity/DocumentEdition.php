<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="document_edition") */
class DocumentEdition
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Document", inversedBy="documentEditions")
	 * @ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")
	 */
	protected $DocumentID;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Edition", inversedBy="documentEditions")
	 * @ORM\JoinColumn(name="EditionCode", referencedColumnName="EditionCode")
	 */
	protected $EditionCode;

	/** @ORM\Id @ORM\Column(length=4) */
	protected $Year;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="CountryEdition", inversedBy="documentEditions")
	 * @ORM\JoinColumn(name="CountryEditionCode", referencedColumnName="CountryEditionCode")
	 */
	protected $CountryEditionCode;

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
	 * @param mixed $DocumentID
	 */
	public function setDocumentID($DocumentID)
	{
		$this->DocumentID = $DocumentID;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentID()
	{
		return $this->DocumentID;
	}

	/**
	 * @param mixed $EditionCode
	 */
	public function setEditionCode($EditionCode)
	{
		$this->EditionCode = $EditionCode;
	}

	/**
	 * @return mixed
	 */
	public function getEditionCode()
	{
		return $this->EditionCode;
	}

	/**
	 * @param mixed $Year
	 */
	public function setYear($Year)
	{
		$this->Year = $Year;
	}

	/**
	 * @return mixed
	 */
	public function getYear()
	{
		return $this->Year;
	}


}