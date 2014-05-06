<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="MoleculeRepository") @ORM\Table(name="molecule") */
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

	/** @ORM\OneToMany(targetEntity="MoleculeName", mappedBy="MoleculeID") */
	protected $moleculeNames;

	/**
	 * @ORM\ManyToMany(targetEntity="Article", mappedBy="molecules")
	 * @ORM\JoinTable(name="article_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")})
	 */
	protected $articles;

	/**
	 * @ORM\ManyToMany(targetEntity="Art", mappedBy="molecules")
	 * @ORM\JoinTable(name="art_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")})
	 */
	protected $arts;

	/**
	 * @ORM\ManyToMany(targetEntity="Publication", mappedBy="molecules")
	 * @ORM\JoinTable(name="publication_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")})
	 */
	protected $publications;

	/**
	 * @ORM\ManyToMany(targetEntity="PharmArticle", mappedBy="molecules")
	 * @ORM\JoinTable(name="pharm_article_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="pharm_article_id", referencedColumnName="id")})
	 */
	protected $pharmArticles;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", mappedBy="molecules")
	 * @ORM\JoinTable(name="document_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	public function __construct()
	{
		$this->documents     = new ArrayCollection();
		$this->moleculeNames = new ArrayCollection();
		$this->articles      = new ArrayCollection();
		$this->arts          = new ArrayCollection();
		$this->publications  = new ArrayCollection();
		$this->pharmArticles = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->MoleculeID . ' - ' . (empty($this->RusName) ? $this->LatName : $this->RusName);
	}

	public function getTitle()
	{
		return empty($this->RusName) ? $this->LatName : $this->RusName;
	}

	public function getId()
	{
		return $this->MoleculeID;
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
	 * @param mixed $moleculeNames
	 */
	public function setMoleculeNames(ArrayCollection $moleculeNames)
	{
		$this->moleculeNames = $moleculeNames;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeNames()
	{
		return $this->moleculeNames;
	}

	/**
	 * @param mixed $articles
	 */
	public function setArticles($articles)
	{
		$this->articles = $articles;
	}

	/**
	 * @return mixed
	 */
	public function getArticles()
	{
		return $this->articles;
	}

	/**
	 * @param mixed $arts
	 */
	public function setArts($arts)
	{
		$this->arts = $arts;
	}

	/**
	 * @return mixed
	 */
	public function getArts()
	{
		return $this->arts;
	}

	/**
	 * @param mixed $publications
	 */
	public function setPublications($publications)
	{
		$this->publications = $publications;
	}

	/**
	 * @return mixed
	 */
	public function getPublications()
	{
		return $this->publications;
	}

	/**
	 * @param mixed $pharmArticles
	 */
	public function setPharmArticles($pharmArticles)
	{
		$this->pharmArticles = $pharmArticles;
	}

	/**
	 * @return mixed
	 */
	public function getPharmArticles()
	{
		return $this->pharmArticles;
	}

	/**
	 * @param mixed $documents
	 */
	public function setDocuments($documents)
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