<?php
// src/Codebender/UserBundle/Entity/User.php

namespace Codebender\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @Assert\Length(max=255, maxMessage="The first name is too long.", groups={"Registration", "Profile"})
	 */
    private $firstname;

    /**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @Assert\Length(max=255, maxMessage="The last name is too long.", groups={"Registration", "Profile"})
	 */
    private $lastname;

    /**
	 * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $karma;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $points;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $referrer_username;

	/**
	 * @ORM\ManyToOne(targetEntity="Codebender\UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=true)
	 **/
	protected $referrer;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $referral_code;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $referrals;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $walkthrough_status;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastWalkthroughDate;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $preregistrationDate;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $registrationDate;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $actualLastLogin;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $editCount;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastEdit;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $compileCount;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastCompile;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $flashCount;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastFlash;

	/**
	 * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
	 */
	private $cloningCount;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastCloning;

	/**
	 * @ORM\Column(type="boolean", nullable=false, options={"default" = false})
	 */
	private $eula;

	/**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }
	
    /**
     * Set twitter
     *
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
	    $this->setKarma(0);
	    $this->setPoints(0);
	    $this->setReferrals(0);
	    $this->setWalkthroughStatus(0);
	    $this->setEditCount(0);
	    $this->setCompileCount(0);
	    $this->setFlashCount(0);
	    $this->setCloningCount(0);
	    $this->setPreregistrationDate(new \DateTime());
	    $this->setEula(true);
    }

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
     * Set karma
     *
     * @param integer $karma
     */
    public function setKarma($karma)
    {
        $this->karma = $karma;
    }

    /**
     * Get karma
     *
     * @return integer 
     */
    public function getKarma()
    {
        return $this->karma;
    }

	/**
	 * Set referrer_username
	 *
	 * @param string $referrerUsername
	 */
	public function setReferrerUsername($referrerUsername)
	{
		$this->referrer_username = $referrerUsername;
	}

	/**
	 * Get referrer_username
	 *
	 * @return string
	 */
	public function getReferrerUsername()
	{
		return $this->referrer_username;
	}

	/**
	 * Set referrer
	 *
	 * @param Codebender\UserBundle\Entity\User $referrer
	 */
	public function setReferrer(\Codebender\UserBundle\Entity\User $referrer)
	{
		$this->referrer = $referrer;
	}

	/**
	 * Get referrer
	 *
	 * @return Codebender\UserBundle\Entity\User
	 */
	public function getReferrer()
	{
		return $this->referrer;
	}

    /**
     * Set referral_code
     *
     * @param string $referralCode
     */
    public function setReferralCode($referralCode)
    {
        $this->referral_code = $referralCode;
    }

    /**
     * Get referral_code
     *
     * @return string 
     */
    public function getReferralCode()
    {
        return $this->referral_code;
    }


    /**
     * Set referrals
     *
     * @param integer $referrals
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     * Get referrals
     *
     * @return integer 
     */
    public function getReferrals()
    {
        return $this->referrals;
    }


    /**
     * Set walkthrough_status
     *
     * @param integer $walkthroughStatus
     */
    public function setWalkthroughStatus($walkthroughStatus)
    {
        $this->walkthrough_status = $walkthroughStatus;
    }

    /**
     * Get walkthrough_status
     *
     * @return integer 
     */
    public function getWalkthroughStatus()
    {
        return $this->walkthrough_status;
    }

    /**
     * Set lastWalkthroughDate
     *
     * @param datetime $lastWalkthroughDate
     */
    public function setLastWalkthroughDate($lastWalkthroughDate)
    {
        $this->lastWalkthroughDate = $lastWalkthroughDate;
    }

    /**
     * Get lastWalkthroughDate
     *
     * @return datetime 
     */
    public function getLastWalkthroughDate()
    {
        return $this->lastWalkthroughDate;
    }

    /**
     * Set registrationDate
     *
     * @param datetime $registrationDate
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * Get registrationDate
     *
     * @return datetime 
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set actualLastLogin
     *
     * @param datetime $actualLastLogin
     */
    public function setActualLastLogin($actualLastLogin)
    {
        $this->actualLastLogin = $actualLastLogin;
    }

    /**
     * Get actualLastLogin
     *
     * @return datetime 
     */
    public function getActualLastLogin()
    {
        return $this->actualLastLogin;
    }

    /**
     * Set editCount
     *
     * @param integer $editCount
     */
    public function setEditCount($editCount)
    {
        $this->editCount = $editCount;
    }

    /**
     * Get editCount
     *
     * @return integer 
     */
    public function getEditCount()
    {
        return $this->editCount;
    }

    /**
     * Set lastEdit
     *
     * @param datetime $lastEdit
     */
    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;
    }

    /**
     * Get lastEdit
     *
     * @return datetime 
     */
    public function getLastEdit()
    {
        return $this->lastEdit;
    }

    /**
     * Set compileCount
     *
     * @param integer $compileCount
     */
    public function setCompileCount($compileCount)
    {
        $this->compileCount = $compileCount;
    }

    /**
     * Get compileCount
     *
     * @return integer 
     */
    public function getCompileCount()
    {
        return $this->compileCount;
    }

    /**
     * Set lastCompile
     *
     * @param datetime $lastCompile
     */
    public function setLastCompile($lastCompile)
    {
        $this->lastCompile = $lastCompile;
    }

    /**
     * Get lastCompile
     *
     * @return datetime 
     */
    public function getLastCompile()
    {
        return $this->lastCompile;
    }

    /**
     * Set flashCount
     *
     * @param integer $flashCount
     */
    public function setFlashCount($flashCount)
    {
        $this->flashCount = $flashCount;
    }

    /**
     * Get flashCount
     *
     * @return integer 
     */
    public function getFlashCount()
    {
        return $this->flashCount;
    }

    /**
     * Set lastFlash
     *
     * @param datetime $lastFlash
     */
    public function setLastFlash($lastFlash)
    {
        $this->lastFlash = $lastFlash;
    }

    /**
     * Get lastFlash
     *
     * @return datetime 
     */
    public function getLastFlash()
    {
        return $this->lastFlash;
    }

    /**
     * Set cloningCount
     *
     * @param integer $cloningCount
     */
    public function setCloningCount($cloningCount)
    {
        $this->cloningCount = $cloningCount;
    }

    /**
     * Get cloningCount
     *
     * @return integer 
     */
    public function getCloningCount()
    {
        return $this->cloningCount;
    }

    /**
     * Set lastCloning
     *
     * @param datetime $lastCloning
     */
    public function setLastCloning($lastCloning)
    {
        $this->lastCloning = $lastCloning;
    }

    /**
     * Get lastCloning
     *
     * @return datetime 
     */
    public function getLastCloning()
    {
        return $this->lastCloning;
    }

    /**
     * Set preregistrationDate
     *
     * @param datetime $preregistrationDate
     */
    public function setPreregistrationDate($preregistrationDate)
    {
        $this->preregistrationDate = $preregistrationDate;
    }

    /**
     * Get preregistrationDate
     *
     * @return datetime 
     */
    public function getPreregistrationDate()
    {
        return $this->preregistrationDate;
    }


    /**
     * Set eula
     *
     * @param boolean $eula
     * @return User
     */
    public function setEula($eula)
    {
        $this->eula = $eula;

        return $this;
    }

    /**
     * Get eula
     *
     * @return boolean
     */
    public function getEula()
    {
        return $this->eula;
    }
}
