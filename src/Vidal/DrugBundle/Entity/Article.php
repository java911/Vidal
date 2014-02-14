<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity @ORM\Table(name="article") */
class Article extends BaseEntity
{
	/** @ORM\Column(length=255) */
	protected $title;

	/** @ORM\Column(type="text", nullable=true) */
	protected $announce;

	/** @ORM\Column(type="text", nullable=true) */
	protected $body;

	/** @ORM\ManyToOne(targetEntity="ArticleRubrique", inversedBy="articles") */
	protected $rubrique;

	/** @ORM\Column(type="boolean") */
	protected $forDoctor;

	/** @ORM\Column(length=255, nullable=true) */
	protected $pubDate;

	/**
	 * @ORM\Column(length=255, nullable=true)
	 */
	protected $link;

	/** @ORM\Column(length=255, nullable=true) */
	protected $author;

	/**
	 * @ORM\ManyToMany(targetEntity="Vidal\DrugBundle\Entity\Nozology", mappedBy="articles")
	 * @ORM\JoinTable(name="article_nozology",
	 * 		joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	public function __construct()
	{

	}

	public function __toString()
	{
		return $this->title;
	}


}