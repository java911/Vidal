<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="marketCache")
 */
class MarketCache
{
    /**
     * @ORM\Id @ORM\Column(type = "integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type ="integer")
     */
    protected $target;

    /**
     * @ORM\Column(type ="boolean")
     */
    protected $document;


    /**
     * @ORM\ManyToMany(targetEntity="MarketDrug", inversedBy="marketCache")
     */
    protected $drugs;


    /**
     * @ORM\Column(type = "datetime")
     * @Gedmo\Timestampable(on = "create")
     */
    protected $created;


    public function __construct(){
        $this->drugs = new ArrayCollection();
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->document;
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
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $drugs
     */
    public function setDrugs($drugs)
    {
        $this->drugs = $drugs;
    }

    /**
     * @return mixed
     */
    public function getDrugs()
    {
        return $this->drugs;
    }

    public function addDrug($drug){
        $this->drugs[] = $drug;
    }

    public function removeDrug($drug){
        $this->drugs->removeElement($drug);
    }

}