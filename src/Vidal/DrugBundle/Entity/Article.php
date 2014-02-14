<?php

namespace Vidal\DrugBundle\Entity;

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
	 * @ORM\ManyToMany(targetEntity="Nozology", mappedBy="articles")
	 * @ORM\JoinTable(name="article_nozology",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	/**
	 * @ORM\ManyToMany(targetEntity="Molecule", mappedBy="articles")
	 * @ORM\JoinTable(name="article_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")})
	 */
	protected $molecules;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", mappedBy="articles")
	 * @ORM\JoinTable(name="article_product",
	 *        joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
	 */
	protected $products;

	/**
	 * @ORM\ManyToOne(targetEntity="ATC", inversedBy="articles")
	 * @ORM\JoinColumn(name="id", referencedColumnName="ATCCode")
	 */
	protected $atc;

	/**
	 * @ORM\ManyToOne(targetEntity="InfoPage", inversedBy="articles")
	 * @ORM\JoinColumn(name="id", referencedColumnName="InfoPageID")
	 */
	protected $infoPage;

	/** @ORM\ManyToOne(targetEntity="ArticleType", inversedBy="articles") */
	protected $type;

	public function __construct()
	{
		$this->nozologies = new ArrayCollection();
		$this->moleculdes = new ArrayCollection();
		$this->products   = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->title;
	}
}