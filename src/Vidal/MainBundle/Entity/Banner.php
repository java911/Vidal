<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="BannerRepository")
 * @ORM\Table(name="banner")
 * @Filestore\Uploadable
 */
class Banner extends BaseEntity
{
	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @Filestore\UploadableField(mapping="banner")
	 */
	protected $banner;

	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @Filestore\UploadableField(mapping="banner")
	 */
	protected $fallback;

	/**
	 * @ORM\Column(length=500)
	 * @Assert\Url(message="Ссылка для баннера указана некорректно")
	 * @Assert\NotBlank(message="Укажите ссылку для баннера")
	 */
	protected $link;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $starts;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $ends;

	/**
	 * @ORM\Column(type="bigint")
	 */
	protected $displayed;

	/**
	 * Сколько осталось показов
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $expires;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $clicks;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $limitDay;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dateDay;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $clickDay;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Range(
	 *      min = 1,
	 *      max = 100,
	 *      minMessage = "Частота появления баннера не может быть меньше {{ limit }}%",
	 *      maxMessage = "Частота появления баннера не может быть больше {{ limit }}%"
	 * )
	 */
	protected $presence;

	/** @ORM\ManyToOne(targetEntity="BannerGroup", inversedBy="banners") */
	protected $group;

	/**
	 * @ORM\ManyToMany(targetEntity="Country", inversedBy="banners")
	 */
	protected $countries;

	/**
	 * @ORM\ManyToMany(targetEntity="City", inversedBy="banners")
	 */
	protected $cities;

	/**
	 * @ORM\OneToOne(targetEntity="Banner")
	 * @ORM\JoinColumn(name="reference_id", referencedColumnName="id")
	 */
	protected $reference;

	public function __construct()
	{
		$this->starts    = new \DateTime();
		$this->clicks    = 0;
		$this->displayed = 0;
		$this->cities    = new ArrayCollection();
		$this->countries = new ArrayCollection();
	}

	public function __toString()
	{
		if (!empty($this->link)) {
			return '[' . $this->id . '] ' . $this->link;
		}
		elseif ($this->id) {
			return '[' . $this->id . ']';
		}
		else {
			return '';
		}
	}

	/**
	 * @param mixed $banner
	 */
	public function setBanner($banner)
	{
		$this->banner = $banner;
	}

	/**
	 * @return mixed
	 */
	public function getBanner()
	{
		return $this->banner;
	}

	/**
	 * @param mixed $clicks
	 */
	public function setClicks($clicks)
	{
		$this->clicks = $clicks;
	}

	/**
	 * @return mixed
	 */
	public function getClicks()
	{
		return $this->clicks;
	}

	/**
	 * @param mixed $expires
	 */
	public function setExpires($expires)
	{
		$this->expires = $expires;
	}

	/**
	 * @return mixed
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	/**
	 * @param mixed $ends
	 */
	public function setEnds($ends)
	{
		$this->ends = $ends;
	}

	/**
	 * @return mixed
	 */
	public function getEnds()
	{
		return $this->ends;
	}

	/**
	 * @param mixed $group
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * @return mixed
	 */
	public function getGroup()
	{
		return $this->group;
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
	 * @param mixed $presence
	 */
	public function setPresence($presence)
	{
		$this->presence = $presence;
	}

	/**
	 * @return mixed
	 */
	public function getPresence()
	{
		return $this->presence;
	}

	/**
	 * @param mixed $starts
	 */
	public function setStarts($starts)
	{
		$this->starts = $starts;
	}

	/**
	 * @return mixed
	 */
	public function getStarts()
	{
		return $this->starts;
	}

	/**
	 * Получение пути хранения изображения баннера
	 *
	 * @return null|string
	 */
	public function getPath()
	{
		return empty($this->banner['path']) ? null : $this->banner['path'];
	}

	/**
	 * @param mixed $displayed
	 */
	public function setDisplayed($displayed)
	{
		$this->displayed = $displayed;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayed()
	{
		return $this->displayed;
	}

	/**
	 * @param mixed $reference
	 */
	public function setReference($reference)
	{
		$this->reference = $reference;
	}

	/**
	 * @return mixed
	 */
	public function getReference()
	{
		return $this->reference;
	}

	public function isSwf()
	{
		return $this->banner['mimeType'] == 'application/x-shockwave-flash';
	}

	/**
	 * @param mixed $limitDay
	 */
	public function setLimitDay($limitDay = 0)
	{
		$this->limitDay = $limitDay;
	}

	/**
	 * @return mixed
	 */
	public function getLimitDay()
	{
		return $this->limitDay;
	}

	/**
	 * @param mixed $clickDay
	 */
	public function setClickDay($clickDay = 0)
	{
		$this->clickDay = $clickDay;
	}

	/**
	 * @return mixed
	 */
	public function getClickDay()
	{
		return $this->clickDay;
	}

	/**
	 * @param mixed $dateDay
	 */
	public function setDateDay($dateDay)
	{
		$this->dateDay = $dateDay;
	}

	/**
	 * @return mixed
	 */
	public function getDateDay()
	{
		return $this->dateDay;
	}

	/**
	 * @param mixed $cities
	 */
	public function setCities($cities)
	{
		$this->cities = $cities;
	}

	/**
	 * @return mixed
	 */
	public function getCities()
	{
		return $this->cities;
	}

	public function addCity($city)
	{
		$this->cities[] = $city;
	}

	public function removeCity($city)
	{
		$this->cities->removeElement($city);
	}

	/**
	 * @param mixed $countries
	 */
	public function setCountries($countries)
	{
		$this->countries = $countries;
	}

	/**
	 * @return mixed
	 */
	public function getCountries()
	{
		return $this->countries;
	}

	public function addCountry($country)
	{
		$this->countries[] = $country;
	}

	public function removeCountry($country)
	{
		$this->countries->removeElement($country);
	}

	/**
	 * @param mixed $fallback
	 */
	public function setFallback($fallback)
	{
		$this->fallback = $fallback;
	}

	/**
	 * @return mixed
	 */
	public function getFallback()
	{
		return $this->fallback;
	}
}