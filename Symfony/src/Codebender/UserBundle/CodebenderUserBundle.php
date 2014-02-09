<?php

namespace Codebender\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CodebenderUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}

}
