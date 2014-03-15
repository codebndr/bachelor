<?php

namespace Codebender\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Codebender\UserBundle\Entity\User;
use Codebender\UserBundle\Handler\MCAPI;


class RegistrationFormHandler extends BaseHandler
{
	/**
	 * Constructor
	 *
	 * @param Symfony\Component\Form\Form $form
	 * @param Symfony\Component\HttpFoundation\Request $request
	 * @param FOS\UserBundle\Model\UserManagerInterface $userManager
	 * @param FOS\UserBundle\Mailer\MailerInterface $mailer
	 * @param FOS\UserBundle\Util\TokenGeneratorInterface $token
	 */
    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $token)
    {
		parent::__construct($form, $request, $userManager, $mailer, $token);
    }

    /**
     * Generates Referrals for New User
	 *
	 * @param String $referrer
	 * @param String $referral_code
	 * @return Rendered Form
	 */
	public function generateReferrals($referrer = null, $referral_code = null)
	{
		if($referrer == null)
			$referrer = $this->request->query->get('referrer');
		if($referral_code == null)
			$referral_code = $this->request->query->get('referral_code');

		$user = new User();

		$user->setReferrerUsername($referrer);
		$user->setReferralCode($referral_code);
		$this->form->setData($user);
		return $this->form;
	}

	/**
	 * Calls parent onSuccess Method
	 */
	protected function onSuccess(UserInterface $user, $confirmation)
	{

		parent::onSuccess($user, $confirmation);

	}
}
