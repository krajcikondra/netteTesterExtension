<?php

namespace Helbrary\NetteTesterExtension;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\SmartObject;

class Authenticator implements IAuthenticator
{

	use SmartObject;

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @param array $credentials
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list( $id, $role ) = $credentials;
		return new Identity( $id, $role, NULL );
	}
}
