<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity @ORM\Table(name="specialty") */
class Specialty
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $id;

	/**
	 * @ORM\OneToMany(targetEntity="User", mappedBy="primarySpecialty")
	 * @ORM\OrderBy({"title"="ASC"})
	 */
	protected $primaryDoctors;

	/**
	 * @ORM\OneToMany(targetEntity="User", mappedBy="secondarySpecialty")
	 * @ORM\OrderBy({"title"="ASC"})
	 */
	protected $secondaryDoctors;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите название специальности.")
	 * @Assert\Length(max=127, maxMessage="Название специальности не может быть длиннее {{limit}} знаков.")
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите сокращенное название специальности.")
	 * @Assert\Length(max=127, maxMessage="Cокращенное название специальности специальности не может быть длиннее {{limit}} знаков.")
	 */
	protected $shortName;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите как назвается врач данной специальности.")
	 * @Assert\Length(max=127, maxMessage="Название врача специальности не может быть длиннее {{limit}} знаков.")
	 */
	protected $doctorName;

	public function __construct()
	{
		$this->primaryDoctors   = new ArrayCollection();
		$this->secondaryDoctors = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->title;
	}

	/**
	 * @param mixed $doctorName
	 */
	public function setDoctorName($doctorName)
	{
		$this->doctorName = $doctorName;
	}

	/**
	 * @return mixed
	 */
	public function getDoctorName()
	{
		return $this->doctorName;
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

	/**
	 * @param mixed $shortName
	 */
	public function setShortName($shortName)
	{
		$this->shortName = $shortName;
	}

	/**
	 * @return mixed
	 */
	public function getShortName()
	{
		return $this->shortName;
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
	 * @param mixed $primaryDoctors
	 */
	public function setPrimaryDoctors($primaryDoctors)
	{
		$this->primaryDoctors = $primaryDoctors;
	}

	/**
	 * @return mixed
	 */
	public function getPrimaryDoctors()
	{
		return $this->primaryDoctors;
	}

	/**
	 * @param mixed $secondaryDoctors
	 */
	public function setSecondaryDoctors($secondaryDoctors)
	{
		$this->secondaryDoctors = $secondaryDoctors;
	}

	/**
	 * @return mixed
	 */
	public function getSecondaryDoctors()
	{
		return $this->secondaryDoctors;
	}
}