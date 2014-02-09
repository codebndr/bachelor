<?php

namespace Codebender\UserBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Codebender\UserBundle\Validator\Constraints\PasswordConstraint;
use Codebender\UserBundle\Validator\Constraints\PasswordConstraintValidator;

class PasswordConstraintValidatorUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PasswordConstraintValidator::isValidPasswordConstraint
     * @covers PasswordConstraintValidator::validate
     * @covers PasswordConstraint
     */

	protected $validator;
    protected $validatorContext;

	protected function setUp()
	{
        $this->validator = new PasswordConstraintValidator;

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
     * @dataProvider validPasswords
     */
    public function testValidPasswords($pass)
    {
 		/* Test valid passwords & if error message is set to null */
        $this->assertTrue($this->validator->validate($pass, new PasswordConstraint()));
	}

	/* Generate valid passwords */
	public function validPasswords()
    {
        return array(
            array('M5%/*gF'),
            array('aAr0nn'),
            array('m4!kt/'),
            array('<val1d>'),
            array('[Djfdk\']'),
            array('*rm-rf/*'),
            array('te$tingWord'),
            array('noMorePass!'),
            array('one1isenough'),
            array('Superpass'),
            array('super`pass'),
            array('super~pass'),
            array('super~pass'),
            array('super!pass'),
            array('super#pass'),
            array('super$pass'),
            array('super%pass'),
            array('super^pass'),
            array('super&pass'),
            array('super*pass'),
            array('super(pass'),
            array('super)pass'),
            array('super-pass'),
            array('super_pass'),
            array('super+pass'),
            array('super=pass'),
            array('super{pass'),
            array('super}pass'),
            array('super[pass'),
            array('super]pass'),
            array('super|pass'),
            array('super:pass'),
            array('super;pass'),
            array('super,pass'),
            array('super<pass'),
            array('super>pass'),
            array('super?pass'),
            array('super/pass'),
            array('super//pass'),
            array('super\'pass'),
            array('super"pass'),
            array('Sup3r/p@ss'),
        );
    }

	/**
     * @dataProvider invalidPasswords
     */
    public function testInvalidPasswords($pass)
    {
		/* Test invalid passwords & if error is set correctly*/
        $this->validatorContext->expects($this->once())->method('addViolation')->with($this->equalTo('Your Password is too simple, try mix and matching Letters, Numbers or Symbols, to make it more secure.'), $this->equalTo(array('{{ pass }}' => $pass)));
		$this->assertFalse($this->validator->validate($pass, new PasswordConstraint()));

    }

    /* Generate invalid passwords */
    public function invalidPasswords()
    {
        return array(
            array('123456'),
            array('password'),
            array('SHALLNOTPASS'),
            array('!@#$}-+@#$<?/{[>'),
            array('this\isnotvalid'),
            array('no space'),
            array(''),
        );
    }

}
