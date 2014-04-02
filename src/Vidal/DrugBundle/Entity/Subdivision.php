<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="SubdivisionRepository") @ORM\Table(name="subdivision") */
class Subdivision
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(length=255, nullable=true) */
	protected $name;

	/** @ORM\Column(length=255, nullable=true) */
	protected $engName;

	/** @ORM\Column(length=255, nullable=true) */
	protected $url;

	/** @ORM\Column(type="text", nullable=true) */
	protected $announce;

	/** @ORM\Column(length=255, nullable=true) */
	protected $title;

	/** @ORM\Column(length=500, nullable=true) */
	protected $description;

	/** @ORM\Column(length=500, nullable=true) */
	protected $keywords;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $parentId;

	/**
	 * @ORM\OneToMany(targetEntity="Subdivision", mappedBy="parent", fetch="EXTRA_LAZY")
	 **/
	protected $children;

	/**
	 * @ORM\ManyToOne(targetEntity="Subdivision", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 **/
	protected $parent;

	/** @ORM\OneToMany(targetEntity="Article", mappedBy="subdivision", fetch="EXTRA_LAZY") */
	protected $arts;

	/** @ORM\OneToMany(targetEntity="Publication", mappedBy="subdivision", fetch="EXTRA_LAZY") */
	protected $publications;

	public function __construct()
	{
		$this->children     = new ArrayCollection();
		$this->arts         = new ArrayCollection();
		$this->publications = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->name;
	}

	/**
	 * @param mixed $engName
	 */
	public function setEngName($engName)
	{
		$this->engName = $engName;
	}

	/**
	 * @return mixed
	 */
	public function getEngName()
	{
		return $this->engName;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
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
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $keywords
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}

	/**
	 * @return mixed
	 */
	public function getKeywords()
	{
		return $this->keywords;
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
	 * @param mixed $parentId
	 */
	public function setParentId($parentId)
	{
		$this->parentId = $parentId;
	}

	/**
	 * @return mixed
	 */
	public function getParentId()
	{
		return $this->parentId;
	}

	/**
	 * @param mixed $children
	 */
	public function setChildren($children)
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
	 * @param mixed $parent
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return $this->parent;
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
}