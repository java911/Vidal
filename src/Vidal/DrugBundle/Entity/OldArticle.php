<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass="OldArticleRepository") @ORM\Table(name="old_article") */
class OldArticle extends BaseEntity
{
	/** @ORM\Column(type="integer", nullable=true) */
	protected $priority;

	/** @ORM\Column(type="text") */
	protected $text;

	/** @ORM\ManyToOne(targetEntity="OldCompany", inversedBy="oldArticles") */
	protected $oldCompany;

	public function __toString()
	{
		return $this->id . '';
	}

	/**
	 * @param mixed $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return mixed
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param mixed $oldCompany
	 */
	public function setOldCompany($oldCompany)
	{
		$this->oldCompany = $oldCompany;
	}

	/**
	 * @return mixed
	 */
	public function getOldCompany()
	{
		return $this->oldCompany;
	}
}