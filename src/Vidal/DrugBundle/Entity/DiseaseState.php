<?php
namespace Vidal\DrugBundle\Entity;

use
    Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity()
 * @ORM\Table(name = "diseasestate")
 */
class DiseaseState extends  BaseEntity
{
    /**
     * @ORM\Column(type = "string")
     */
    protected  $title;

    /**
     * @ORM\ManyToMany(targetEntity = "Disease", inversedBy="states")
     * @ORM\JoinTable(name="disease_disease_state",
     * 		joinColumns={@ORM\JoinColumn(name="state_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="disease_id", referencedColumnName="id")})
     */
    protected $diseases;

    /**
     * @ORM\Column(type = "integer", nullable = true)
     */
    protected  $offerId;

    protected $body;

    /**
     * @ORM\ManyToMany(targetEntity = "Article", mappedBy="states")
     * @ORM\JoinTable(name="diseaseStateArticle",
     * 		joinColumns={@ORM\JoinColumn(name="state_id", referencedColumnName="id")},
     * 		inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")})
     */
    protected $articles;

    public function __construct(){
        $this->diseases = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function __toString(){
        return $this->title;
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

    public function addDisease(Disease $disease){
        $this->diseases[] = $disease;
    }

    public function removeDisease(Disease $disease){
        $this->diseases->removeElement($disease);
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
     * @param mixed $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @return mixed
     */
    public function getArticles()
    {
        return $this->articles;
    }

    public function addArticle($article)
    {
        $this->articles[] = $article;
    }

    public function removeArticle($article)
    {
        $this->articles->removeElement($article);
    }
}