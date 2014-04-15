<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="product_document") */
class ProductDocument
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\ManyToOne(targetEntity="Document", inversedBy="productDocument")
	 * @ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")
	 */
	protected $DocumentID;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="productDocument")
	 * @ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")
	 */
	protected $ProductID;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;
}