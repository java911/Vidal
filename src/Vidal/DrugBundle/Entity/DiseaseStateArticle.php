<?php
namespace Vidal\DrugBundle\Entity;

use
    Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\Table(name = "diseasestate_article")
 */
class DiseaseStateArticle extends  BaseEntity
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $articleId;

    /**
     * @ORM\ManyToOne(targetEntity="DiseaseState", inversedBy="articles")
     */
    protected $diseaseState;

    /**
     * @param mixed $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return mixed
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param mixed $diseaseState
     */
    public function setDiseaseState($diseaseState)
    {
        $this->diseaseState = $diseaseState;
    }

    /**
     * @return mixed
     */
    public function getDiseaseState()
    {
        return $this->diseaseState;
    }



}