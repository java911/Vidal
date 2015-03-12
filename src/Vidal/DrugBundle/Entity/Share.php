<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity() @ORM\Table(name="share") */
class Share extends BaseEntity
{
	/**
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="shares")
	 * @ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")
	 */
	protected $ProductID;

	/** @ORM\ManyToOne(targetEntity="Art", inversedBy="shares") */
	protected $art;

	/** @ORM\ManyToOne(targetEntity="Article", inversedBy="shares") */
	protected $article;

	/** @ORM\ManyToOne(targetEntity="Publication", inversedBy="shares") */
	protected $publication;

	/**
	 * @return mixed
	 */
	public function getProductID()
	{
		return $this->ProductID;
	}

	/**
	 * @param mixed $ProductID
	 */
	public function setProductID($ProductID)
	{
		$this->ProductID = $ProductID;
	}

	/**
	 * @return mixed
	 */
	public function getArt()
	{
		return $this->art;
	}

	/**
	 * @param mixed $art
	 */
	public function setArt($art)
	{
		$this->art = $art;
	}

	/**
	 * @return mixed
	 */
	public function getArticle()
	{
		return $this->article;
	}

	/**
	 * @param mixed $article
	 */
	public function setArticle($article)
	{
		$this->article = $article;
	}

	/**
	 * @return mixed
	 */
	public function getPublication()
	{
		return $this->publication;
	}

	/**
	 * @param mixed $publication
	 */
	public function setPublication($publication)
	{
		$this->publication = $publication;
	}
}