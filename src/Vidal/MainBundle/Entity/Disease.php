<?php
namespace Vidal\MainBundle\Entity;

use
	Doctrine\ORM\Mapping as ORM,
	Symfony\Component\Validator\Constraints as Assert,
	Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass = "DiseaseRepository")
 * @ORM\Table(name = "disease")
 */
class Disease extends BaseEntity
{
	/**
	 * @ORM\Column(type = "string")
	 */
	protected $title;

	/**
	 * @ORM\ManyToMany(targetEntity = "DiseaseSymptom", inversedBy="diseases")
	 * @ORM\JoinTable(name="Disease_symptom_disease",
	 *        joinColumns={@ORM\JoinColumn(name="disease_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="symptom_id", referencedColumnName="id")})
	 */
	protected $symptoms;

	/**
	 * @ORM\Column(type = "integer", nullable = true)
	 */
	protected $offerId;

	/**
	 * @ORM\ManyToMany(targetEntity = "DiseaseState", mappedBy="diseases")
	 * @ORM\JoinTable(name="Disease_disease_state",
	 *        joinColumns={@ORM\JoinColumn(name="disease_id", referencedColumnName="id")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="state_id", referencedColumnName="id")})
	 */
	protected $states;

	public function __construct()
	{
		$this->symptoms = new ArrayCollection();
		$this->states   = new ArrayCollection();
	}

	/**
	 * @param mixed $diseaseParts
	 */
	public function setSymptoms($symptom)
	{
		$this->symptoms = $symptom;
		return $this;
	}

	/**
	 * @param mixed $diseaseParts
	 */
	public function addSymptom($symptom)
	{
		$this->symptoms[] = $symptom;
		return $this;
	}

	/**
	 * @param mixed $diseaseParts
	 */
	public function removeSymptom($symptom)
	{
		$this->symptoms->removeElement($symptom);
	}

	/**
	 * @return mixed
	 */
	public function getSymptoms()
	{
		return $this->symptoms;
	}

	/**
	 * @param mixed $clinics
	 */
	public function setClinics($clinics)
	{
		$this->clinics = $clinics;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getClinics()
	{
		return $this->clinics;
	}

	/**
	 * @param mixed $drugs
	 */
	public function setDrugs($drugs)
	{
		$this->drugs = $drugs;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDrugs()
	{
		return $this->drugs;
	}

	/**
	 * @param mixed $offerId
	 */
	public function setOfferId($offerId)
	{
		$this->offerId = $offerId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOfferId()
	{
		return $this->offerId;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $states
	 */
	public function setStates($states)
	{
		$this->states = $states;
	}

	/**
	 * @return mixed
	 */
	public function getStates()
	{
		return $this->states;
	}

	public function addState(DiseaseState $state)
	{
		$this->states[] = $state;
	}

	public function removeState(DiseaseState $state)
	{
		$this->states->removeElement($state);
	}
}