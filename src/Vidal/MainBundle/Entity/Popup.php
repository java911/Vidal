<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="PopupRepository")
 * @ORM\Table(name="Popup")
 * @Filestore\Uploadable
 */
class Popup extends BaseEntity
{

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Filestore\UploadableField(mapping="popup")
     */
    protected $image;

    /**
     * @ORM\Column(length=500)
     * @Assert\NotBlank(message="Укажите название поп-апа")
     */
    protected $title;

    /**
     * @ORM\Column(length=500)
     * @Assert\Url(message="Ссылка для баннера указана некорректно")
     * @Assert\NotBlank(message="Укажите ссылку для баннера")
     */
    protected $link;

    /**
     * @Assert\NotBlank(message="Укажите частоту показа поп-апа в сутки")
     * @ORM\Column(type="integer")
     */
    protected $frequency;

    /**
     * @ORM\Column(type="integer")
     */
    protected $counter = 0;

    /**
     * @return mixed
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param mixed $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }




}