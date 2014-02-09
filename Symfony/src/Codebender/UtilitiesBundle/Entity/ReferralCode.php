<?php
// src/Codebender/ProjectBundle/Entity/ReferralCode.php

namespace Codebender\UtilitiesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class ReferralCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
	 * @ORM\Column(type="string", length=255)
	 */
    private $name;

    /**
	 * @ORM\Column(type="string", length=500, options={"default"=""})
	 */
    private $description;

    /**
	 * @ORM\Column(type="string", length=255)
     */
    private $code;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $points;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $issued;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
    private $available;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

	/**
	 * Set points
	 *
	 * @param integer $points
	 */
	public function setPoints($points)
	{
		$this->points = $points;
	}

	/**
	 * Get points
	 *
	 * @return integer
	 */
	public function getPoints()
	{
		return $this->points;
	}

	/**
     * Set issued
     *
     * @param integer $issued
     */
    public function setIssued($issued)
    {
        $this->issued = $issued;
    }

    /**
     * Get issued
     *
     * @return integer 
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * Set available
     *
     * @param integer $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * Get available
     *
     * @return integer 
     */
    public function getAvailable()
    {
        return $this->available;
    }
}
