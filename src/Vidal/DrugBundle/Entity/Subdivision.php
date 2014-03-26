<?php

namespace Vidal\DrugBundle\Entity;

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
}