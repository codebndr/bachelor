<?php
// src/Codebender/ProjectBundle/Entity/Board.php

namespace Codebender\BoardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Codebender\UserBundle\Entity\User;

/**
 * @ORM\Entity
 */
class Board
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
	 * @ORM\Column(type="string", length=300)
     */
    private $upload;

    /**
	 * @ORM\Column(type="string", length=300)
	 */
    private $bootloader;

    /**
	 * @ORM\Column(type="string", length=300)
     */
    private $build;

    /**
     * @ORM\ManyToOne(targetEntity="Codebender\UserBundle\Entity\User")
     **/
	protected $owner;

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
     * Set upload
     *
     * @param string $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }

    /**
     * Get upload
     *
     * @return string 
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * Set bootloader
     *
     * @param string $bootloader
     */
    public function setBootloader($bootloader)
    {
        $this->bootloader = $bootloader;
    }

    /**
     * Get bootloader
     *
     * @return string 
     */
    public function getBootloader()
    {
        return $this->bootloader;
    }

    /**
     * Set build
     *
     * @param string $build
     */
    public function setBuild($build)
    {
        $this->build = $build;
    }

    /**
     * Get build
     *
     * @return string 
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set owner
     *
     * @param Codebender\UserBundle\Entity\User $owner
     */
    public function setOwner(\Codebender\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return Codebender\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}