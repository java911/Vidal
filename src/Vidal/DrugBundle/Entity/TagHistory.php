<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/** @ORM\Entity(repositoryClass="TagHistoryRepository") @ORM\Table(name="tag_history") */
class TagHistory extends BaseEntity
{
	/** @ORM\Column(length=255) */
	protected $text;

	/** @ORM\ManyToOne(targetEntity="Tag", inversedBy="history") */
	protected $tag;

	/** @ORM\Column(type="boolean") */
	protected $any = false;

	public function __toString()
	{
		return $this->text;
	}

	/**
	 * @param mixed $any
	 */
	public function setAny($any)
	{
		$this->any = $any;
	}

	/**
	 * @return mixed
	 */
	public function getAny()
	{
		return $this->any;
	}

	/**
	 * @param mixed $tag
	 */
	public function setTag($tag)
	{
		$this->tag = $tag;
	}

	/**
	 * @return mixed
	 */
	public function getTag()
	{
		return $this->tag;
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
}