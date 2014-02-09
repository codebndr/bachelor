<?php

namespace Codebender\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UsernameConstraint extends Constraint
{
	public $message = 'Your Username contains invalid characters. Please try again with a different one.';
}
