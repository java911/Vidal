<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="molecule") */
class Molecule
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $MoleculeID;

	/** @ORM\Column(length=500) */
	protected $LatName;

	/** @ORM\Column(length=500, nullable=true) */
	protected $RusName;

	/**
	 * @ORM\ManyToOne(targetEntity="MoleculeBase", inversedBy="molecules")
	 * @ORM\JoinColumn(name="GNParent", referencedColumnName="GNParent")
	 */
	protected $GNParent;

	/**
	 * @ORM\ManyToOne(targetEntity="MarketStatus", inversedBy="molecules")
	 * @ORM\JoinColumn(name="MarketStatusID", referencedColumnName="MarketStatusID")
	 */
	protected $MarketStatusID;

	/** @ORM\Column(length=500, nullable=true, name="GDDB_MoleculeName") */
	protected $gddbMoleculeName;

	/** @ORM\Column(type="integer", nullable=true, name="GDDB_MOLECULENAMEID") */
	protected $gddbMoleculeNameID;

	/** @ORM\Column(type="integer", nullable=true, name="GDDB_MoleculeID") */
	protected $gddbMoleculeID;

	/** @ORM\OneToMany(targetEntity="MoleculeDocument", mappedBy="MoleculeID") */
	protected $moleculeDocuments;

	public function __construct()
	{
		$this->moleculeDocuments = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->LatName;
	}

	/**
	 * @param mixed $GNParent
	 */
	public function setGNParent($GNParent)
	{
		$this->GNParent = $GNParent;
	}

	/**
	 * @return mixed
	 */
	public function getGNParent()
	{
		return $this->GNParent;
	}

	/**
	 * @param mixed $LatName
	 */
	public function setLatName($LatName)
	{
		$this->LatName = $LatName;
	}

	/**
	 * @return mixed
	 */
	public function getLatName()
	{
		return $this->LatName;
	}

	/**
	 * @param mixed $MarketStatusID
	 */
	public function setMarketStatusID($MarketStatusID)
	{
		$this->MarketStatusID = $MarketStatusID;
	}

	/**
	 * @return mixed
	 */
	public function getMarketStatusID()
	{
		return $this->MarketStatusID;
	}

	/**
	 * @param mixed $MoleculeID
	 */
	public function setMoleculeID($MoleculeID)
	{
		$this->MoleculeID = $MoleculeID;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeID()
	{
		return $this->MoleculeID;
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
	 * @param mixed $gddbMoleculeID
	 */
	public function setGddbMoleculeID($gddbMoleculeID)
	{
		$this->gddbMoleculeID = $gddbMoleculeID;
	}

	/**
	 * @return mixed
	 */
	public function getGddbMoleculeID()
	{
		return $this->gddbMoleculeID;
	}

	/**
	 * @param mixed $gddbMoleculeName
	 */
	public function setGddbMoleculeName($gddbMoleculeName)
	{
		$this->gddbMoleculeName = $gddbMoleculeName;
	}

	/**
	 * @return mixed
	 */
	public function getGddbMoleculeName()
	{
		return $this->gddbMoleculeName;
	}

	/**
	 * @param mixed $gddbMoleculeNameID
	 */
	public function setGddbMoleculeNameID($gddbMoleculeNameID)
	{
		$this->gddbMoleculeNameID = $gddbMoleculeNameID;
	}

	/**
	 * @return mixed
	 */
	public function getGddbMoleculeNameID()
	{
		return $this->gddbMoleculeNameID;
	}

	/**
	 * @param mixed $moleculeDocuments
	 */
	public function setMoleculeDocuments(ArrayCollection $moleculeDocuments)
	{
		$this->moleculeDocuments = $moleculeDocuments;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeDocuments()
	{
		return $this->moleculeDocuments;
	}
}