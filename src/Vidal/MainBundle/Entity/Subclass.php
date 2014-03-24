<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="subclass") */
class Subclass
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(length=255, nullable=true) */
	protected $name;

	/** @ORM\Column(length=255, nullable=true) */
	protected $engName;

	/** @ORM\OneToMany(targetEntity="Publication", mappedBy="Subclass") */
	protected $publications;

	/** @ORM\ManyToOne(targetEntity="Subdivision", inversedBy="Subclasses") */
	protected $subdivision;

	public function __construct()
	{
		$this->publications = new ArrayCollection();
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
	 * @param mixed $subdivision
	 */
	public function setSubdivision($subdivision)
	{
		$this->subdivision = $subdivision;
	}

	/**
	 * @return mixed
	 */
	public function getSubdivision()
	{
		return $this->subdivision;
	}
}