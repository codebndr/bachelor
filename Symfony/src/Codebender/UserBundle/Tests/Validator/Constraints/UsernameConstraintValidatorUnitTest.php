<?php

namespace Codebender\UserBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Codebender\UserBundle\Validator\Constraints\UsernameConstraint;
use Codebender\UserBundle\Validator\Constraints\UsernameConstraintValidator;

class UsernameConstraintValidatorUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers UsernameConstraintValidator::isValidUsernameConstraint
     * @covers UsernameConstraintValidator::validate
     * @covers UsernameConstraint
     */

	protected $validator;
    protected $validatorContext;

	protected function setUp()
	{
        $this->validator = new UsernameConstraintValidator;

        $this->validatorContext = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->setMethods('addViolation')
            ->getMockForAbstractClass();

        $this->validator->initialize($this->validatorContext);
	}

	protected function tearDown()
	{
		$this->validator = null;
        $this->validatorContext = null;
	}

	/**
     * @dataProvider validUsernames
     */
    public function testValidUsernames($username)
    {
        $this->assertTrue($this->validator->validate($username, new UsernameConstraint()));
	}

	public function validUsernames()
    {
        return array(
            array('[Djfdk\']'),
            array('*rm-rf/*'),
            array('username'),
            array('Us3rName! '),
            array('one1isenough'),
            array('Supername'),
            array('user`name'),
            array('user~name'),
            array('user~name'),
            array('user!name'),
            array('user#name'),
            array('user$name'),
            array('user%name'),
            array('user^name'),
            array('user*name'),
            array('user(name'),
            array('user)name'),
            array('user-name'),
            array('user_name'),
            array('user+name'),
            array('user=name'),
            array('user{name'),
            array('user}name'),
            array('user[name'),
            array('user]name'),
            array('user|name'),
            array('user:name'),
            array('user;name'),
            array('user,name'),
            array('user?name'),
            array('user/name'),
            array('user//name'),
            array('user\'name'),
            array('@username'),
        );
    }

	/**
     * @dataProvider invalidUsernames
     */
    public function testInvalidUsernames($username)
    {
		/* Test invalid namewords & if error is set correctly*/
        $this->validatorContext->expects($this->once())->method('addViolation')->with($this->equalTo('Your Username contains invalid characters. Please try again with a different one.'), $this->equalTo(array('{{ username }}' => $username)));
		$this->assertFalse($this->validator->validate($username, new UsernameConstraint()));

    }

    public function invalidUsernames()
    {
        return array(
            array('null'),
            array('<invalid>'),
            array('!@#$}-+@#$<?/{[>'),
            array('user&name'),
            array('user"name'),
        );
    }

}
