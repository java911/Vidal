<?php
namespace Vidal\DrugBundle\Entity;

use
    Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity()
 * @ORM\Table(name = "diseasesymptom")
 */
class DiseaseSymptom extends  BaseEntity
{
    /**
     * @ORM\Column(type = "string")
     */
    protected  $title;

    /**
     * @ORM\ManyToMany(targetEntity = "DiseaseParty", inversedBy="symptoms")
     * @ORM\JoinTable(name="disease_part_symptom",
     *      joinColumns={@ORM\JoinColumn(name="symptomy_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="party_id", referencedColumnName="id")})
     */
    protected $parts;

    /**
     * @ORM\ManyToMany(targetEntity = "Disease", mappedBy="symptoms")
     * @ORM\JoinTable(name="Disease_symptom_disease",
     * 		joinColumns={@ORM\JoinColumn(name="symptom_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="disease_id", referencedColumnName="id")})
     */
    protected $diseases;


    /**
     * @ORM\Column(type = "integer", nullable = true)
     */
    protected  $offerId;



    public function __construct(){
        $this->parts = new ArrayCollection();
    }


    /**
     * @param mixed $diseaseParts
     */
    public function setParts($diseaseParts)
    {
        $this->parts = $diseaseParts;
        return $this;
    }

    /**
     * @param mixed $diseaseParts
     */
    public function addParts($diseasePart)
    {
        $this->parts[] = $diseasePart;
        return $this;
    }

    /**
     * @param mixed $diseaseParts
     */
    public function removeParts($diseasePart)
    {
        $this->parts->removeElement($diseasePart);
    }

    /**
     * @return mixed
     */
    public function getDiseaseParts()
    {
        return $this->parts;
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
     * @param mixed $diseases
     */
    public function setDiseases($diseases)
    {
        $this->diseases = $diseases;
    }

    /**
     * @return mixed
     */
    public function getDiseases()
    {
        return $this->diseases;
    }





}