<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="edition") */
class Edition
{
	/** @ORM\Id @ORM\Column(length=4) */
	protected $EditionCode;

	/** @ORM\Column(length=100) */
	protected $Name;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="editions")
	 * @ORM\JoinTable(name="documentoc_edition",
	 *        joinColumns={@ORM\JoinColumn(name="EditionCode", referencedColumnName="EditionCode")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/** @ORM\OneToMany(targetEntity="DocumentEdition", mappedBy="EditionCode") */
	protected $documentEditions;

	public function __construct()
	{
		$this->documents        = new ArrayCollection();
		$this->documentEditions = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->EditionCode;
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
	 * @param mixed $Name
	 */
	public function setName($Name)
	{
		$this->Name = $Name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->Name;
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

	/**
	 * @param mixed $documents
	 */
	public function setDocuments(ArrayCollection $documents)
	{
		$this->documents = $documents;
	}

	/**
	 * @return mixed
	 */
	public function getDocuments()
	{
		return $this->documents;
	}
}