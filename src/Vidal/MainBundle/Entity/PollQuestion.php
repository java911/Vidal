<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="PollQuestion")
 * @Filestore\Uploadable
 */
class PollQuestion extends BaseEntity
{
    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity = "Poll", inversedBy = "questions")
     */
    protected $poll;

    /**
     * @ORM\OneToMany(targetEntity = "PollAnswer", mappedBy = "poll")
     */
    protected $answers;

    public function __construct(){
        $this->answers = new ArrayCollection();
    }

    public function addAnswer($answer){
        $this->answers[] = $answer;
    }

    public function removeAnswer($answer){
        $this->answers->removeElement($answer);
    }

    /**
     * @param mixed $poll
     */
    public function setPoll($poll)
    {
        $this->poll = $poll;
    }

    /**
     * @return mixed
     */
    public function getPoll()
    {
        return $this->poll;
    }



    /**
     * @param mixed $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
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
}
