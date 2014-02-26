<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="ArticleRepository") @ORM\Table(name="article") */
class Article extends BaseEntity
{
	/** @ORM\Column(length=255) */
	protected $title;

	/** @ORM\Column(type="text", nullable=true) */
	protected $announce;

	/** @ORM\Column(type="text", nullable=true) */
	protected $body;

	/** @ORM\ManyToOne(targetEntity="ArticleRubrique", inversedBy="articles") */
	protected $rubrique;

	/** @ORM\Column(type="boolean") */
	protected $forDoctor;

	/**
	 * @ORM\Column(length=255, nullable=true)
	 * @Assert\Regex(
	 *     pattern="/[a-zA-Z\d\-\_\.]+/",
	 *     match=true,
	 *     message="Ссылка может состоять только из латинских букв, цифр, тире, точки и подчеркивания."
	 * )
	 */
	protected $link;

	/** @ORM\Column(length=255, nullable=true) */
	protected $author;

	/**
	 * @ORM\ManyToMany(targetEntity="Nozology", inversedBy="articles")
	 * @ORM\JoinTable(name="article_nozology",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	/**
	 * @ORM\ManyToMany(targetEntity="Molecule", inversedBy="articles")
	 * @ORM\JoinTable(name="article_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")})
	 */
	protected $molecules;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", inversedBy="articles")
	 * @ORM\JoinTable(name="article_product",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
	 */
	protected $products;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="articles")
	 * @ORM\JoinTable(name="article_document",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/**
	 * @ORM\ManyToOne(targetEntity="ATC", inversedBy="articles")
	 * @ORM\JoinColumn(name="atc", referencedColumnName="ATCCode")
	 */
	protected $atc;

	/**
	 * @ORM\ManyToOne(targetEntity="InfoPage", inversedBy="articles")
	 * @ORM\JoinColumn(name="infoPage", referencedColumnName="InfoPageID")
	 */
	protected $infoPage;

	/** @ORM\ManyToOne(targetEntity="ArticleType", inversedBy="articles") */
	protected $type;

	public function __construct()
	{
		$this->nozologies = new ArrayCollection();
		$this->molecules  = new ArrayCollection();
		$this->products   = new ArrayCollection();
		$this->documents  = new ArrayCollection();
		$this->forDoctor  = false;
		$this->author     = 'Доктор Видаль: медицинская энциклопедия www.vidal.ru';
		$now              = new \DateTime('now');
		$this->created    = $now;
		$this->updated    = $now;
	}

	public function __toString()
	{
		return empty($this->title) ? '' : $this->title;
	}

	/**
	 * @param mixed $announce
	 */
	public function setAnnounce($announce)
	{
		$this->announce = $announce;
	}

	/**
	 * @return mixed
	 */
	public function getAnnounce()
	{
		return $this->announce;
	}

	/**
	 * @param mixed $atc
	 */
	public function setAtc($atc)
	{
		$this->atc = $atc;
	}

	/**
	 * @return mixed
	 */
	public function getAtc()
	{
		return $this->atc;
	}

	/**
	 * @param mixed $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
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
	 * @param mixed $forDoctor
	 */
	public function setForDoctor($forDoctor)
	{
		$this->forDoctor = $forDoctor;
	}

	/**
	 * @return mixed
	 */
	public function getForDoctor()
	{
		return $this->forDoctor;
	}

	/**
	 * @param mixed $infoPage
	 */
	public function setInfoPage($infoPage)
	{
		$this->infoPage = $infoPage;
	}

	/**
	 * @return mixed
	 */
	public function getInfoPage()
	{
		return $this->infoPage;
	}

	/**
	 * @param mixed $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}

	/**
	 * @return mixed
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param mixed $molecules
	 */
	public function setMolecules($molecules)
	{
		$this->molecules = $molecules;
	}

	/**
	 * @return mixed
	 */
	public function getMolecules()
	{
		return $this->molecules;
	}

	/**
	 * @param mixed $nozologies
	 */
	public function setNozologies($nozologies)
	{
		$this->nozologies = $nozologies;
	}

	/**
	 * @return mixed
	 */
	public function getNozologies()
	{
		return $this->nozologies;
	}

	/**
	 * @param mixed $products
	 */
	public function setProducts($products)
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
	 * @param mixed $rubrique
	 */
	public function setRubrique($rubrique)
	{
		$this->rubrique = $rubrique;
	}

	/**
	 * @return mixed
	 */
	public function getRubrique()
	{
		return $this->rubrique;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	public function addDocument(Document $document)
	{
		$this->documents[] = $document;
	}
}