<?php
// src/Codebender/ProjectBundle/Entity/ViewLog.php

namespace Codebender\UtilitiesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Codebender\UserBundle\Entity\User;
/**
 * @ORM\Entity
 */
class ViewLog
{
    const HOME_PAGE_VIEW = 1;
    const LIBRARY_PAGE_VIEW = 2;
    const BOARDS_PAGE_VIEW = 3;
    const LIBRARY_EXAMPLE_VIEW = 4;
    const ABOUT_PAGE_VIEW = 5;
    const TECH_PAGE_VIEW = 6;
    const TEAM_PAGE_VIEW = 7;
    const TUTORIALS_PAGE_VIEW = 8;
    const PRIVATE_PROJECTS_INFO_VIEW = 9;
    const UPLOAD_BOOTLOADER_VIEW = 10;
    const WALKTHROUGH_VIEW = 11;
    const EDITOR_PROJECT_VIEW = 12;
    const EMBEDDED_PROJECT_VIEW = 13;
    const JSON_PROJECT_VIEW = 14;
    const PROJECT_VIEW = 15;
    const EMBEDDED_LIBRARY_EXAMPLE_VIEW = 16;

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
     * @ORM\Column(type="string")
     */
    private $session;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasPreviousSession;


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

    /**
     * Set session
     *
     * @param string $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set hasPreviousSession
     *
     * @param boolean $hasPreviousSession
     */
    public function setHasPreviousSession($hasPreviousSession)
    {
        $this->hasPreviousSession = $hasPreviousSession;
    }

    /**
     * Get hasPreviousSession
     *
     * @return boolean
     */
    public function getHasPreviousSession()
    {
        return $this->hasPreviousSession;
    }
}