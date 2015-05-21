<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="DeliveryOpenRepository") @ORM\Table(name="delivery_open") */
class DeliveryOpen extends BaseEntity
{
	/** @ORM\ManyToOne(targetEntity="User", inversedBy="deliveryOpen") */
	protected $user;

	/** @ORM\ManyToOne(targetEntity="Delivery", inversedBy="deliveryOpen") */
	protected $delivery;

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	/**
	 * @return mixed
	 */
	public function getDelivery()
	{
		return $this->delivery;
	}

	/**
	 * @param mixed $delivery
	 */
	public function setDelivery($delivery)
	{
		$this->delivery = $delivery;
	}
}