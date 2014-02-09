<?php

namespace Codebender\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'attr' => array(
                'placeholder' => 'Username',
                'style' => 'max-width:100%'
            )))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array(
                'placeholder' => 'Email',
                'style' => 'max-width:100%'
            )))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password', 'attr' => array('placeholder' => 'Type a password', 'style' => 'max-width:100%')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('placeholder' => 'Repeat password', 'style' => 'max-width:100%')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
			->add('firstname', 'text', array('label' => 'user_registration_form_firstname',	'required' => false))
			->add('lastname', 'text', array('label' => 'user_registration_form_lastname', 'required' => false))
			->add('twitter', 'text', array('label' => 'user_registration_form_twitter',	'required' => false))
			->add('referrer_username', 'text', array('label' => 'user_registration_form_referrer', 'required' => false))
			->add('referral_code', 'text', array('label' => 'user_registration_form_referral_code', 'required' => false));
    }

    public function getName()
    {
        return 'codebender_user_registration';
    }
}

