<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="DigestRepository") @ORM\Table(name="digest") */
class Digest extends BaseEntity
{
	/** @ORM\Column(type="boolean") */
	protected $progress = false;

	/** @ORM\Column(type="text", nullable=true) */
	protected $text;

	/** @ORM\Column(type="string", length=255) */
	protected $subject;

	/** @ORM\Column(type="string", length=500, nullable=true) */
	protected $emails;

	/** @ORM\ManyToMany(targetEntity="Specialty", inversedBy="digests") */
	protected $specialties;

	/** @ORM\Column(type="boolean") */
	protected $allSpecialties = true;

	public function __construct()
	{
		$this->specialties = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getProgress()
	{
		return $this->progress;
	}

	/**
	 * @param mixed $progress
	 */
	public function setProgress($progress)
	{
		$this->progress = $progress;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
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
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @param mixed $subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * @return mixed
	 */
	public function getSpecialties()
	{
		return $this->specialties;
	}

	/**
	 * @param mixed $specialties
	 */
	public function setSpecialties($specialties)
	{
		$this->specialties = $specialties;
	}

	/**
	 * @return mixed
	 */
	public function getEmails()
	{
		return $this->emails;
	}

	/**
	 * @param mixed $emails
	 */
	public function setEmails($emails)
	{
		$this->emails = $emails;
	}

	/**
	 * @return mixed
	 */
	public function getAllSpecialties()
	{
		return $this->allSpecialties;
	}

	/**
	 * @param mixed $allSpecialties
	 */
	public function setAllSpecialties($allSpecialties)
	{
		$this->allSpecialties = $allSpecialties;
	}
}