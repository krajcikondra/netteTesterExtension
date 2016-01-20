<?php

namespace Helbrary\NetteTesterExtension;

class Exception extends \Exception
{}

class UnexpectedRedirectResponse extends Exception
{
	function __construct($message = 'For check redirect response use method checkRedirectToSignIn')
	{
		parent::__construct($message);
	}
}
