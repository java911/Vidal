<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="ContraindicationRepository") @ORM\Table(name="index_contraindication") */
class Contraindication
{
	/** @ORM\Id @ORM\Column(length=10) */
	protected $ContraIndicCode;

	/** @ORM\Column(type="text") */
	protected $RusName;

	/** @ORM\Column(type="text") */
	protected $EngName;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="contraindications")
	 * @ORM\JoinTable(name="document_contraindication",
	 *        joinColumns={@ORM\JoinColumn(name="ContraIndicCode", referencedColumnName="ContraIndicCode")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	public function __construct()
	{
		$this->documents = new ArrayCollection();
	}

	/**
	 * @param mixed $ContraIndicCode
	 */
	public function setContraIndicCode($ContraIndicCode)
	{
		$this->ContraIndicCode = $ContraIndicCode;
	}

	/**
	 * @return mixed
	 */
	public function getContraIndicCode()
	{
		return $this->ContraIndicCode;
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