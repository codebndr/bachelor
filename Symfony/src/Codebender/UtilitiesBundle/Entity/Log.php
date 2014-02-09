<?php
// src/Codebender/ProjectBundle/Entity/Log.php

namespace Codebender\UtilitiesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Codebender\UserBundle\Entity\User;
/**
 * @ORM\Entity
 */
class Log
{

    const CREATE_PROJECT = 0;
    const CLONE_PROJECT = 1;
    const EDIT_PROJECT = 2;
    const DELETE_PROJECT = 3;
    const UPLOAD_PROJECT = 4;
    const DOWNLOAD_PROJECT = 5;
    const COMPILE_PROJECT = 6;
    const FLASH_PROJECT = 7;
    const CHANGE_PROJECT_PERMISSIONS = 8;
    const CLONE_LIB_EXAMPLE = 9;
    const SEARCH=10;
    const CREATE_FILE = 11;
    const DELETE_FILE = 12;
    const CHANGE_PROJECT_DESCRIPTION = 13;
    const CHANGE_PROJECT_NAME = 14;
    const CHANGE_FILE_NAME = 15;
    const CLOUD_FLASH_BUTTON = 16;
    const WEBSERIAL_MONITOR_BUTTON = 17;
    const SERIAL_MONITOR_BUTTON = 18;
    const PREREGISTRATION = 19;
    const REGISTRATION = 20;
    const VERIFY_PROJECT = 21;
    const UPLOAD_BOARD = 22;
    const DELETE_BOARD = 23;
    const EDIT_BOARD = 24;
    const UPLOAD_BOOTLOADER_BUTTON = 25;
    const DOWNLOAD_HEX = 26;
    const PLUGIN_INFO = 27;
    const OS_BROWSER_INFO = 28;
    const UPLOAD_FILE = 29;
    const DOWNLOAD_LIBRARY_EXAMPLE = 30;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user;

    /**
	 * @ORM\Column(type="smallint")
	 */
    private $action;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
	private $metadata;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;




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
     * Set user
     *
     * @param integer $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set action
     *
     * @param smallint $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return smallint 
     */
    public function getAction()
    {
        return $this->action;
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
     * Set metadata
     *
     * @param string $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get metadata
     *
     * @return string 
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set timestamp
     *
     * @param datetime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Get timestamp
     *
     * @return datetime 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
