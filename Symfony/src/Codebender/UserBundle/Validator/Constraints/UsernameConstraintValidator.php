<?php

namespace Codebender\UserBundle\Validator\Constraints;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class UsernameConstraintValidator extends ConstraintValidator
{

    private function isValidUsername($username)
    {

        if( htmlspecialchars($username) == $username && $username!= 'null')
			return true;
		else
			return false;
	}

 
    public function validate($username, Constraint $constraint)
    {
        if( $this->isValidUsername($username) === false ) {
            $this->context->addViolation($constraint->message, array(
                '{{ username }}' => $username
            ));
            return false;
        }
        return true;
    }
}
