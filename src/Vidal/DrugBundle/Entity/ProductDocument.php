<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="product_document") */
class ProductDocument
{
	/** @ORM\Column(type="integer") */
	protected $DocumentID;

	/** @ORM\Column(type="integer") */
	protected $ProductID;

	/** @ORM\Column(type="smallint", nullable=true) */
	protected $Ranking;
}