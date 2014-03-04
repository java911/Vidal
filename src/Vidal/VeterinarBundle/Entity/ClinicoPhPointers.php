<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="clinicophpointers") */
class ClinicoPhPointers
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $ClPhPointerID;

	/** @ORM\Column(length=50) */
	protected $Code;

	/** @ORM\Column(length=255) */
	protected $Name;

	/** @ORM\Column(type="smallint") */
	protected $Level;

	/** @ORM\Column(type="boolean") */
	protected $ShowInExport = false;

	/** @ORM\Column(type="boolean") */
	protected $UsedInSp = false;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="clphPointers")
	 * @ORM\JoinTable(name="document_clphpointers",
	 *        joinColumns={@ORM\JoinColumn(name="ClPhPointerID", referencedColumnName="ClPhPointerID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	public function __construct()
	{
		$this->documents = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->Name;
	}

	/**
	 * @param mixed $ClPhPointerID
	 */
	public function setClPhPointerID($ClPhPointerID)
	{
		$this->ClPhPointerID = $ClPhPointerID;
	}

	/**
	 * @return mixed
	 */
	public function getClPhPointerID()
	{
		return $this->ClPhPointerID;
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
	 * @param mixed $ShowInExport
	 */
	public function setShowInExport($ShowInExport)
	{
		$this->ShowInExport = $ShowInExport;
	}

	/**
	 * @return mixed
	 */
	public function getShowInExport()
	{
		return $this->ShowInExport;
	}

	/**
	 * @param mixed $UserInSp
	 */
	public function setUserInSp($UserInSp)
	{
		$this->UserInSp = $UserInSp;
	}

	/**
	 * @return mixed
	 */
	public function getUserInSp()
	{
		return $this->UserInSp;
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