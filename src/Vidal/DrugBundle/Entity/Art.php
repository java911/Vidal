<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;

/** @ORM\Entity(repositoryClass="ArtRepository") @ORM\Table(name="art") @FileStore\Uploadable */
class Art extends BaseEntity
{
	/** @ORM\Column(length=255) */
	protected $title;

	/** @ORM\Column(type="text", nullable=true) */
	protected $announce;

	/** @ORM\Column(type="text", nullable=true) */
	protected $body;

	/**
	 * @ORM\Column(length=255, nullable=true)
	 * @Assert\Regex(
	 *     pattern="/[a-zA-Z\d\-\_\.]+/",
	 *     match=true,
	 *     message="Ссылка может состоять только из латинских букв, цифр, тире, точки и подчеркивания."
	 * )
	 */
	protected $link;

	/**
	 * @ORM\ManyToMany(targetEntity="Nozology", inversedBy="arts")
	 * @ORM\JoinTable(name="art_n",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	/**
	 * @ORM\ManyToMany(targetEntity="Molecule", inversedBy="arts")
	 * @ORM\JoinTable(name="art_molecule",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="MoleculeID", referencedColumnName="MoleculeID")})
	 */
	protected $molecules;

	/**
	 * @ORM\ManyToMany(targetEntity="Document", inversedBy="arts")
	 * @ORM\JoinTable(name="art_document",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")})
	 */
	protected $documents;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", inversedBy="arts")
	 * @ORM\JoinTable(name="art_product",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
	 */
	protected $products;

	/**
	 * @ORM\ManyToMany(targetEntity="ATC", inversedBy="arts")
	 * @ORM\JoinTable(name="art_atc",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")})
	 */
	protected $atcCodes;

	/**
	 * @ORM\ManyToMany(targetEntity="InfoPage", inversedBy="arts")
	 * @ORM\JoinTable(name="art_infopage",
	 *        joinColumns={@ORM\JoinColumn(name="art_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="InfoPageID", referencedColumnName="InfoPageID")})
	 */
	protected $infoPages;

	/** @ORM\Column(type="datetime", nullable=true) */
	protected $date;

	/** @ORM\Column(length=500, nullable=true) */
	protected $synonym;

	/** @ORM\Column(length=255, nullable=true) */
	protected $metaTitle;

	/** @ORM\Column(length=255, nullable=true) */
	protected $metaDescription;

	/** @ORM\Column(length=255, nullable=true) */
	protected $metaKeywords;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $priority;

	/** @ORM\ManyToMany(targetEntity="Tag", inversedBy="arts") */
	protected $tags;

	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="video")
	 * @Assert\File(
	 *        maxSize="100M",
	 *        maxSizeMessage="Видео не может быть больше 100Мб",
	 *        mimeTypesMessage="Видео должно быть в формате .flv"
	 * )
	 */
	protected $video;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoWidth;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoHeight;

	/** @ORM\Column(length=10, nullable=true) */
	protected $hidden;

	/** @ORM\ManyToOne(targetEntity="ArtRubrique", inversedBy="arts") */
	protected $rubrique;

	/** @ORM\ManyToOne(targetEntity="ArtType", inversedBy="arts") */
	protected $type;

	/** @ORM\ManyToOne(targetEntity="ArtCategory", inversedBy="arts") */
	protected $category;

	/** @ORM\Column(type="boolean") */
	protected $atIndex = false;

	/** @ORM\Column(type="boolean") */
	protected $anons = false;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $anonsPriority;

	/** @ORM\Column(type="boolean") */
	protected $hideDate = false;

	/** @ORM\Column(type="boolean") */
	protected $testMode = false;

	/** @ORM\Column(type="text", nullable=true) */
	protected $code;

	/** @ORM\ManyToMany(targetEntity="Video", inversedBy="arts", cascade={"persist"}) */
	protected $videos;

	public function __construct()
	{
		$this->nozologies = new ArrayCollection();
		$this->molecules  = new ArrayCollection();
		$this->documents  = new ArrayCollection();
		$this->products   = new ArrayCollection();
		$this->atcCodes   = new ArrayCollection();
		$this->infoPages  = new ArrayCollection();
		$this->tags       = new ArrayCollection();
		$this->videos     = new ArrayCollection();

		$now           = new \DateTime('now');
		$this->created = $now;
		$this->updated = $now;
		$this->date    = $now;
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
	 * @param mixed $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}

	/**
	 * @return mixed
	 */
	public function getLink()
	{
		return $this->link;
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

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$title = str_replace('<p>', '', $title);
		$title = str_replace('</p>', '', $title);
		$title = str_replace('<div>', '', $title);
		$title = str_replace('</div>', '', $title);

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
	 * @param mixed $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
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
	 * @param mixed $synonym
	 */
	public function setSynonym($synonym)
	{
		$this->synonym = $synonym;
	}

	/**
	 * @return mixed
	 */
	public function getSynonym()
	{
		return $this->synonym;
	}

	/**
	 * @param mixed $metaDescription
	 */
	public function setMetaDescription($metaDescription)
	{
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return mixed
	 */
	public function getMetaDescription()
	{
		return $this->metaDescription;
	}

	/**
	 * @param mixed $metaKeywords
	 */
	public function setMetaKeywords($metaKeywords)
	{
		$this->metaKeywords = $metaKeywords;
	}

	/**
	 * @return mixed
	 */
	public function getMetaKeywords()
	{
		return $this->metaKeywords;
	}

	/**
	 * @param mixed $metaTitle
	 */
	public function setMetaTitle($metaTitle)
	{
		$this->metaTitle = $metaTitle;
	}

	/**
	 * @return mixed
	 */
	public function getMetaTitle()
	{
		return $this->metaTitle;
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
	 * @param mixed $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @return mixed
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param mixed $atIndex
	 */
	public function setAtIndex($atIndex)
	{
		$this->atIndex = $atIndex;
	}

	/**
	 * @return mixed
	 */
	public function getAtIndex()
	{
		return $this->atIndex;
	}

	/**
	 * @param mixed $anonsPriority
	 */
	public function setAnonsPriority($anonsPriority)
	{
		$this->anonsPriority = $anonsPriority;
	}

	/**
	 * @return mixed
	 */
	public function getAnonsPriority()
	{
		return $this->anonsPriority;
	}

	/**
	 * @param mixed $anons
	 */
	public function setAnons($anons)
	{
		$this->anons = $anons;
	}

	/**
	 * @return mixed
	 */
	public function getAnons()
	{
		return $this->anons;
	}

	public function isArticle()
	{
		return false;
	}

	public function getT()
	{
		return 'Art';
	}

	/**
	 * @param mixed $products
	 */
	public function setProducts($products)
	{
		$this->products = $products;
	}

	/**
	 * @return mixed
	 */
	public function getProducts()
	{
		return $this->products;
	}

	public function addProduct(Product $product)
	{
		if (!$this->products->contains($product)) {
			$this->products[] = $product;
		}
	}

	public function removeProduct(Product $product)
	{
		$this->products->removeElement($product);
	}

	/**
	 * @param mixed $hideDate
	 */
	public function setHideDate($hideDate)
	{
		$this->hideDate = $hideDate;
	}

	/**
	 * @return mixed
	 */
	public function getHideDate()
	{
		return $this->hideDate;
	}

	/**
	 * @param mixed $testMode
	 */
	public function setTestMode($testMode)
	{
		$this->testMode = $testMode;
	}

	/**
	 * @return mixed
	 */
	public function getTestMode()
	{
		return $this->testMode;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	public function addAtcCode($atcCode)
	{
		$this->atcCodes[] = $atcCode;

		return $this;
	}

	public function removeAtcCode($atcCode)
	{
		$this->atcCodes->removeElement($atcCode);
	}

	public function addNozology($nozology)
	{
		$this->nozologies[] = $nozology;

		return $this;
	}

	public function removeNozology($nozology)
	{
		$this->nozologies->removeElement($nozology);

		return $this;
	}

	public function addMolecule($molecule)
	{
		$this->molecules[] = $molecule;

		return $this;
	}

	public function removeMolecule($molecule)
	{
		$this->molecules->removeElement($molecule);

		return $this;
	}

	public function addInfoPage($infoPage)
	{
		$this->infoPages[] = $infoPage;

		return $this;
	}

	public function removeInfoPage($infoPage)
	{
		$this->infoPages->removeElement($infoPage);

		return $this;
	}

	/**
	 * @param mixed $videos
	 */
	public function setVideos($videos)
	{
		$this->videos = $videos;
	}

	/**
	 * @return mixed
	 */
	public function getVideos()
	{
		return $this->videos;
	}

	public function addVideo(Video $video)
	{
		if (!$this->videos->contains($video)) {
			$this->videos[] = $video;
		}

		return $this;
	}

	public function removeVideo(Video $video)
	{
		$this->videos->removeElement($video);
	}
}