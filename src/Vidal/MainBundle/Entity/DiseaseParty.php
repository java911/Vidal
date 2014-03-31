<?php
namespace Vidal\MainBundle\Entity;

use
    Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity()
 * @ORM\Table(name = "diseaseparty")
 */
class DiseaseParty extends  BaseEntity
{
    /**
     * @ORM\Column(type = "string")
     */
    protected  $title;


    /**
     * @ORM\ManyToMany(targetEntity = "DiseaseSymptom", mappedBy="parts")
     * @ORM\JoinTable(name="disease_part_symptom",
     *      joinColumns={@ORM\JoinColumn(name="party_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="symptomy_id", referencedColumnName="id")})
     */
    protected $symptoms;

    /**
     * @ORM\Column(type = "integer", nullable = true)
     */
    protected  $offerId;

    /**
     *
     * @ORM\Column(type = "integer")
     */
    protected $sex;

    public function __construct(){
        $this->symptoms = new ArrayCollection();
    }

    /**
     * @param mixed $diseases
     */
    public function setSymptoms($symptom)
    {
        $this->symptoms = $symptom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSymptoms()
    {
        return $this->symptoms;
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
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
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

}