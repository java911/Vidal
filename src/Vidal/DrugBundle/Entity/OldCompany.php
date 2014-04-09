<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="OldCompanyRepository") @ORM\Table(name="old_company") */
class OldCompany
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(length=255) */
	protected $title;

	/** @ORM\OneToMany(targetEntity="OldArticle", mappedBy="oldCompany") */
	protected $oldArticles;

	public function __construct()
	{
		$this->oldArticles = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->title;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
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
	 * @param mixed $oldArticles
	 */
	public function setOldArticles($oldArticles)
	{
		$this->oldArticles = $oldArticles;
	}

	/**
	 * @return mixed
	 */
	public function getOldArticles()
	{
		return $this->oldArticles;
	}
}