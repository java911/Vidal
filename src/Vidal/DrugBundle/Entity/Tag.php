<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/** @ORM\Entity(repositoryClass="TagRepository") @ORM\Table(name="tag") @UniqueEntity("text") */
class Tag
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(length=255, unique=true) */
	protected $text;

	/** @ORM\Column(length=255, nullable=true) */
	protected $search;

	/** @ORM\ManyToMany(targetEntity="Article", mappedBy="tags", fetch="EXTRA_LAZY") */
	protected $articles;

	/** @ORM\ManyToMany(targetEntity="Art", mappedBy="tags", fetch="EXTRA_LAZY") */
	protected $arts;

	/** @ORM\ManyToMany(targetEntity="Publication", mappedBy="tags", fetch="EXTRA_LAZY") */
	protected $publications;

	/** @ORM\ManyToMany(targetEntity="PharmArticle", mappedBy="tags", fetch="EXTRA_LAZY") */
	protected $pharmArticles;

	public function __construct()
	{
		$this->articles      = new ArrayCollection();
		$this->arts          = new ArrayCollection();
		$this->publications  = new ArrayCollection();
		$this->pharmArticles = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->text;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = trim($text);
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
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
	 * @param mixed $search
	 */
	public function setSearch($search)
	{
		$this->search = trim($search);
	}

	/**
	 * @return mixed
	 */
	public function getSearch()
	{
		return $this->search;
	}
}