<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="NozologyRepository") @ORM\Table(name="nozology") */
class Nozology
{
	/** @ORM\Id @ORM\Column(length=8) */
	protected $NozologyCode;

	/** @ORM\Column(length=8, nullable=true) */
	protected $Code;

	/** @ORM\Column(length=500, nullable=true) */
	protected $Name;

	/** @ORM\Column(length=3, nullable=true) */
	protected $Level;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="nozologies")
	 * @ORM\JoinTable(name="document_indicnozology",
	 *        joinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/** @ORM\Column(length=20, nullable=true) */
	protected $Class;

	public function __construct()
	{
		$this->documents = new ArrayCollection;
	}

	public function __toString()
	{
		return $this->Name;
	}

	/**
	 * @param mixed $Code
	 */
	public function setCode($Code)
	{
		$this->Code = $Code;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->Code;
	}

	/**
	 * @param mixed $NozologyCode
	 */
	public function setNozologyCode($NozologyCode)
	{
		$this->NozologyCode = $NozologyCode;
	}

	/**
	 * @return mixed
	 */
	public function getNozologyCode()
	{
		return $this->NozologyCode;
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
	 * @param mixed $Level
	 */
	public function setLevel($Level)
	{
		$this->Level = $Level;
	}

	/**
	 * @return mixed
	 */
	public function getLevel()
	{
		return $this->Level;
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

	/**
	 * @param mixed $class
	 */
	public function setClass($class)
	{
		$this->Class = $class;
	}

	/**
	 * @return mixed
	 */
	public function getClass()
	{
		return $this->Class;
	}
}