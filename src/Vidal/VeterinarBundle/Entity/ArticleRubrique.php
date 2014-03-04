<?php

namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="ArticleRubriqueRepository") @ORM\Table(name="article_rubrique") */
class ArticleRubrique
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(length=255, unique=true) */
	protected $title;

	/**
	 * @ORM\Column(length=255, nullable=true, unique=true)
	 * @Assert\Regex(
	 *     pattern="/[a-z\-]+/",
	 *     match=true,
	 *     message="Путь к рубрике может состоять только из латинских букв и тире"
	 * )
	 */
	protected $rubrique;

	/**
	 * @ORM\OneToMany(targetEntity="Article", mappedBy="rubrique")
	 */
	protected $articles;

	public function __construct()
	{
		$this->articles = new ArrayCollection();
	}

	public function __toString()
	{
		return empty($this->title) ? '' : $this->title;
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

}