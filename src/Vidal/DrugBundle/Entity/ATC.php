<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="ATCRepository") @ORM\Table(name="atc") */
class ATC
{
	/** @ORM\Id @ORM\Column(length=10, unique=true) */
	protected $ATCCode;

	/** @ORM\Column(length=255) */
	protected $RusName;

	/** @ORM\Column(length=255) */
	protected $EngName;

	/** @ORM\Column(length=10) @ORM\ManyToOne(targetEntity="ATC", inversedBy="children") */
	protected $ParentATCCode;

	/** @ORM\Column(type="boolean") */
	protected $ShowInExport = false;

	/** @ORM\OneToMany(targetEntity="ATC", mappedBy="ParentATCCode") */
	protected $children;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", mappedBy="atcCodes")
	 * @ORM\JoinTable(name="product_atc",
	 *        joinColumns={@ORM\JoinColumn(name="ATCcode", referencedColumnName="ATCCode")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
	 */
	protected $products;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="atcCodes")
	 * @ORM\JoinTable(name="documentoc_atc",
	 *      joinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/**
	 * @ORM\ManyToMany(targetEntity="Article", mappedBy="atcCodes")
	 * @ORM\JoinTable(name="article_atc",
	 *        joinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")})
	 */
	protected $articles;

	/**
	 * @ORM\ManyToMany(targetEntity="Art", mappedBy="atcCodes")
	 * @ORM\JoinTable(name="art_atc",
	 *        joinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")})
	 */
	protected $arts;

	public function __construct()
	{
		$this->children = new ArrayCollection();
		$this->products = new ArrayCollection();
		$this->articles = new ArrayCollection();
		$this->arts     = new ArrayCollection();
	}

	public function getId()
	{
		return $this->ATCCode;
	}

	public function __toString()
	{
		return $this->ATCCode . ' - ' . $this->RusName;
	}

	/**
	 * @param mixed $ATCCode
	 */
	public function setATCCode($ATCCode)
	{
		$this->ATCCode = $ATCCode;
	}

	/**
	 * @return mixed
	 */
	public function getATCCode()
	{
		return $this->ATCCode;
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
	 * @param mixed $ParentATCCode
	 */
	public function setParentATCCode($ParentATCCode)
	{
		$this->ParentATCCode = $ParentATCCode;
	}

	/**
	 * @return mixed
	 */
	public function getParentATCCode()
	{
		return $this->ParentATCCode;
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
	 * @param mixed $children
	 */
	public function setChildren(ArrayCollection $children)
	{
		$this->children = $children;
	}

	/**
	 * @return mixed
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @param mixed $products
	 */
	public function setProducts(ArrayCollection $products)
	{
		$this->products = $products;
	}

	/**
	 * @return mixed
	 */
	public function getProducts()
	{
		return $this->products;
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
}