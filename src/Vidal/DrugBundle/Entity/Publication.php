<?php

namespace Vidal\DrugBundle\Entity;

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
	 *        maxSize="4M",
	 *    maxSizeMessage="Принимаются фотографии размером до 4 Мб"
	 * )
	 */
	protected $photo;

	/** @ORM\Column(length=500) */
	protected $title;

	/** @ORM\Column(type="text", nullable=true) */
	protected $announce;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(length=255, nullable=true) */
	protected $keyword;

	/** @ORM\Column(type="datetime", nullable=true) */
	protected $date;

	/**
	 * @ORM\ManyToMany(targetEntity="Nozology", inversedBy="publications", orphanRemoval=true)
	 * @ORM\JoinTable(name="publication_n",
	 *        joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	/**
	 * @ORM\ManyToMany(targetEntity="Molecule", inversedBy="publications", orphanRemoval=true)
	 * @ORM\JoinTable(name="publication_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")})
	 */
	protected $molecules;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="publications", orphanRemoval=true)
	 * @ORM\JoinTable(name="publication_document",
	 *        joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/**
	 * @ORM\ManyToMany(targetEntity="ATC", inversedBy="publications", orphanRemoval=true)
	 * @ORM\JoinTable(name="publication_atc",
	 *        joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")})
	 */
	protected $atcCodes;

	/**
	 * @ORM\ManyToMany(targetEntity="InfoPage", inversedBy="publications", orphanRemoval=true)
	 * @ORM\JoinTable(name="publication_infopage",
	 *        joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")})
	 */
	protected $infoPages;

	/** @ORM\ManyToMany(targetEntity="Tag", inversedBy="publications", orphanRemoval=true) */
	protected $tags;

	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="video")
	 * @Assert\File(
	 * 		maxSize="100M",
	 * 		maxSizeMessage="Видео не может быть больше 100Мб",
	 * 		mimeTypesMessage="Видео должно быть в формате .flv"
	 * )
	 */
	protected $video;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoWidth;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoHeight;

	/** @ORM\Column(length=10, nullable=true) */
	protected $hidden;

	/** @ORM\Column(length=10, nullable=true) */
	protected $hidden2;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $priority;

	public function __construct()
	{
		$now              = new \DateTime('now');
		$this->created    = $now;
		$this->updated    = $now;
		$this->date       = $now;
		$this->nozologies = new ArrayCollection();
		$this->molecules  = new ArrayCollection();
		$this->documents  = new ArrayCollection();
		$this->atcCodes   = new ArrayCollection();
		$this->infoPages  = new ArrayCollection();
		$this->tags       = new ArrayCollection();
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
	 * @param mixed $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}

	/**
	 * @return mixed
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param mixed $atcCodes
	 */
	public function setAtcCodes($atcCodes)
	{
		$this->atcCodes = $atcCodes;
	}

	/**
	 * @return mixed
	 */
	public function getAtcCodes()
	{
		return $this->atcCodes;
	}

	/**
	 * @param mixed $documents
	 */
	public function setDocuments($documents)
	{
		$this->documents = $documents;
	}

	/**
	 * @return mixed
	 */
	public function getDocuments()
	{
		return $this->documents;
	}

	/**
	 * @param mixed $infoPages
	 */
	public function setInfoPages($infoPages)
	{
		$this->infoPages = $infoPages;
	}

	/**
	 * @return mixed
	 */
	public function getInfoPages()
	{
		return $this->infoPages;
	}

	/**
	 * @param mixed $molecules
	 */
	public function setMolecules($molecules)
	{
		$this->molecules = $molecules;
	}

	/**
	 * @return mixed
	 */
	public function getMolecules()
	{
		return $this->molecules;
	}

	/**
	 * @param mixed $nozologies
	 */
	public function setNozologies($nozologies)
	{
		$this->nozologies = $nozologies;
	}

	/**
	 * @return mixed
	 */
	public function getNozologies()
	{
		return $this->nozologies;
	}

	public function addDocument(Document $document)
	{
		if (!$this->documents->contains($document)) {
			$this->documents[] = $document;
		}

		return $this;
	}

	public function removeDocument(Document $document)
	{
		$this->documents->removeElement($document);
	}

	/**
	 * @param mixed $tags
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @return mixed
	 */
	public function getTags()
	{
		return $this->tags;
	}

	public function addTag(Tag $tag)
	{
		if (!$this->tags->contains($tag)) {
			$this->tags[] = $tag;
		}

		return $this;
	}

	public function removeTag($tag)
	{
		$this->tags->removeElement($tag);
	}

	/**
	 * @param mixed $video
	 */
	public function setVideo($video)
	{
		$this->video = $video;
	}

	/**
	 * @return mixed
	 */
	public function getVideo()
	{
		return $this->video;
	}

	/**
	 * @param mixed $videoHeight
	 */
	public function setVideoHeight($videoHeight)
	{
		$this->videoHeight = $videoHeight;
	}

	/**
	 * @return mixed
	 */
	public function getVideoHeight()
	{
		return $this->videoHeight;
	}

	/**
	 * @param mixed $videoWidth
	 */
	public function setVideoWidth($videoWidth)
	{
		$this->videoWidth = $videoWidth;
	}

	/**
	 * @return mixed
	 */
	public function getVideoWidth()
	{
		return $this->videoWidth;
	}

	/**
	 * @param mixed $hidden
	 */
	public function setHidden($hidden)
	{
		$this->hidden = $hidden;
	}

	/**
	 * @return mixed
	 */
	public function getHidden()
	{
		return $this->hidden;
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
	 * @param mixed $hidden2
	 */
	public function setHidden2($hidden2)
	{
		$this->hidden2 = $hidden2;
	}

	/**
	 * @return mixed
	 */
	public function getHidden2()
	{
		return $this->hidden2;
	}

	public function getT()
	{
		return 'Publication';
	}
}