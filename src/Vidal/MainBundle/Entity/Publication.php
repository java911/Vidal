<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="PublicationRepository") @ORM\Table(name="publication") @FileStore\Uploadable */
class Publication extends BaseEntity
{
	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="photo")
	 * @Assert\Image(
	 * 		maxSize="4M",
	 *  	maxSizeMessage="Принимаются фотографии размером до 4 Мб"
	 * )
	 */
	protected $photo;

	/** @ORM\Column(length=500) */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $announce;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\ManyToOne(targetEntity="User", inversedBy="publications") */
	protected $author;

	/** @ORM\Column(length=255, nullable=true) */
	protected $keyword;

	/** @ORM\ManyToOne(targetEntity="Subdivision", inversedBy="publications") */
	protected $subdivision;

	/** @ORM\ManyToOne(targetEntity="Subclass", inversedBy="publications") */
	protected $subclass;

	public function __construct()
	{
		$now = new \DateTime('now');
		$this->created = $now;
		$this->updated = $now;
	}

	public function __toString()
	{
		return empty($this->title) ? '' : $this->title;
	}

	/**
	 * @param mixed $announce
	 */
	public function setAnnounce($announce)
	{
		$this->announce = $announce;
	}

	/**
	 * @return mixed
	 */
	public function getAnnounce()
	{
		return $this->announce;
	}

	/**
	 * @param mixed $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor()
	{
		return $this->author;
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
	 * @param mixed $photo
	 */
	public function setPhoto($photo)
	{
		$this->photo = $photo;
	}

	/**
	 * @return mixed
	 */
	public function getPhoto()
	{
		return $this->photo;
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
	 * @param mixed $subdivision
	 */
	public function setSubdivision($subdivision)
	{
		$this->subdivision = $subdivision;
	}

	/**
	 * @return mixed
	 */
	public function getSubdivision()
	{
		return $this->subdivision;
	}

	/**
	 * @param mixed $keyword
	 */
	public function setKeyword($keyword)
	{
		$this->keyword = $keyword;
	}

	/**
	 * @return mixed
	 */
	public function getKeyword()
	{
		return $this->keyword;
	}

	/**
	 * @param mixed $subclass
	 */
	public function setSubclass($subclass)
	{
		$this->subclass = $subclass;
	}

	/**
	 * @return mixed
	 */
	public function getSubclass()
	{
		return $this->subclass;
	}
}