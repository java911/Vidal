<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PharmPortfolioRepository")
 * @ORM\Table(name="pharm_portfolio")
 * @UniqueEntity("url")
 */
class PharmPortfolio extends BaseEntity
{
	/** @ORM\Column(type="integer", nullable=true) */
	protected $priority;

	/** @ORM\Column(length=255, unique=true) */
	protected $url;

	/** @ORM\Column(length=255) */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\ManyToOne(targetEntity="PharmCompany", inversedBy="portfolios") */
	protected $company;

	/**
	 * @ORM\ManyToOne(targetEntity="Document", inversedBy="portfolios")
	 * @ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")
	 */
	protected $DocumentID;

	public function __toString()
	{
		return $this->title;
	}

	/**
	 * @param mixed $DocumentID
	 */
	public function setDocumentID($DocumentID)
	{
		$this->DocumentID = $DocumentID;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentID()
	{
		return $this->DocumentID;
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
	 * @param mixed $company
	 */
	public function setCompany($company)
	{
		$this->company = $company;
	}

	/**
	 * @return mixed
	 */
	public function getCompany()
	{
		return $this->company;
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