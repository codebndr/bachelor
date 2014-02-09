<?php

namespace Codebender\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Collection;


class OptionsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		/* create the form*/
        $builder
            ->add('username', 'text', array(
                                            'label' => 'Username',
                                            'read_only' => true,
											'attr'=> array(
													'disabled'=>true,
													'class' => 'option-form-input')))
            ->add('firstname', 'text',array('label' => 'Firstname',
                                            'attr' => array(
													'class' => 'option-form-input')))
            ->add('lastname', 'text',array('label' => 'Lastname',
                                           'attr' => array(
                                                    'class' => 'option-form-input')))
            ->add('email', 'email', array('label' => 'Email',
                                          'attr' => array(
                                                    'class' => 'option-form-input')))
            ->add('twitter', 'text', array('label' => 'Twitter',
                                            'required' => false,
											'attr' => array(
                                                    'class' => 'option-form-input')))
            ->add('currentPassword', 'password', array(
														'label' => 'Current Password',
														'required' => false,
														'attr'=> array(
																'max_length' => 255,
																'placeholder'=> 'Type your current password',
																'class' => 'option-form-input')))
            ->add('plainPassword', 'repeated', array(
													'label' => 'New Password',
													'type' => 'password',
													'invalid_message' => 'The New Password fields must match.',
													'first_name' => 'new',
													'second_name' => 'confirm',
													'required' => false,
													'options' => array(
																'attr' => array(
																		'max_length' => 255,
																		'placeholder'=> 'Type your new password',
																		'class' => 'option-form-input')),
													));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $nameReg = '/^[a-zA-Z_\'-\s]*$/'; /* allow letters,spaces and the _ ' - chars in names */
        $constraints = new Collection(array(
            'fields' => array(
                'firstname' => array(
                    new Regex( array(
                        'pattern' => $nameReg,
                        'match' => true,
                        'message' => 'Sorry, your Firstname can only contain a letters and \' - _'
                    )),
                ),
                'lastname' => array(
                    new Regex( array(
                        'pattern' => $nameReg,
                        'match' => true,
                        'message' => 'Sorry, your Lastname can only contain a letters and \' - _'
                    )),
                ),
                'email' => array(
                    new NotBlank(array('message' => 'Please fill in your Email address')),
                    new Email(array('message' => 'Sorry, this is not a valid Email address', 'checkMX' => true)),
                ),
                'plainPassword' => array(
                    new Length(array('min' => 6, 'minMessage' => 'Sorry, New Password must be at least 6 characters long',
                                    'max' => 255, 'maxMessage' => 'Sorry, New Password cannot be longer than 255 characters'))
                ),
            ),
            'allowExtraFields' => true,
            'allowMissingFields' => true,
        ));

        $resolver->setDefaults(array(
            'constraints' => $constraints,
        ));

    }

    public function getName()
    {
        return 'options';
    }
    
}
