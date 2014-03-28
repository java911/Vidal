<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="subdivision") */
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

	/** @ORM\OneToMany(targetEntity="Publication", mappedBy="Subdivision") */
	protected $publications;

	/** @ORM\OneToMany(targetEntity="Subclass", mappedBy="Subclass") */
	protected $subclasses;

	/**
	 * @ORM\OneToOne(targetEntity="Subdivision")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	protected $parent;

	public function __construct()
	{
		$this->publications = new ArrayCollection();
		$this->subclasses = new ArrayCollection();
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
	 * @param mixed $subclasses
	 */
	public function setSubclasses($subclasses)
	{
		$this->subclasses = $subclasses;
	}

	/**
	 * @return mixed
	 */
	public function getSubclasses()
	{
		return $this->subclasses;
	}
}