<?php

namespace Helbrary\NetteTesterExtension;

use Nette\Configurator;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Tester\Assert;
use Tracy\Debugger;


abstract class BasePresenterTester extends \Tester\TestCase
{

	/**
	 * @var \Nette\DI\Container
	 */
	protected $container;

	/**
	 * @var \Nette\Application\LinkGenerator
	 */
	protected $linkGenerator;

	/**
	 * @var \Nette\Application\IPresenterFactory
	 */
	protected $presenterFactory;

	/** @var  Authenticator */
	protected $authenticator;

	public function __construct()
	{
		$this->container = require __DIR__ . '/../../../../app/bootstrap.php';
		$this->linkGenerator = $this->container->getByType('Nette\Application\LinkGenerator');
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
		$this->authenticator = new Authenticator();
		$configurator = new Configurator();
	}

	/**
	 * Return presenter
	 * @param string $presenter - etc. 'Front:GoodsChange:Goods'
	 * @param bool $autoCanonicalize
	 * @return \Nette\Application\IPresenter
	 */
	public function getPresenter($presenter, $autoCanonicalize = FALSE)
	{
		$presenter = $this->presenterFactory->createPresenter($presenter);
		$presenter->autoCanonicalize = $autoCanonicalize;
		return $presenter;
	}

	/**
	 * Create and send request
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $method
	 * @param array $parameters
	 * @param null|int $userId
	 * @return \Nette\Application\IResponse
	 */
	public function sendRequest($presenterName, $method = 'GET', $parameters = array(), $userId = NULL)
	{
		$presenter = $this->getPresenter($presenterName);
		if ($userId !== NULL) {
			$presenter->user->setAuthenticator($this->authenticator);
			$presenter->user->login($userId, '');
		}
		$request = new \Nette\Application\Request($presenterName, $method, $parameters);
		/** @var $response \Nette\Application\Responses\RedirectResponse */
		return $presenter->run($request);
	}

	/**
	 * Check if request is without error
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $method
	 * @param array $parameters
	 * @param null|int $userId
	 */
	public function checkRequestNoError($presenterName, $method = 'GET', $parameters = array(), $userId = NULL)
	{
		Assert::noError(function() use ($presenterName, $method, $parameters, $userId) {
			$this->sendRequest($presenterName, $method, $parameters, $userId);
		});
	}

	/**
	 * Check if request is without error
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $method
	 * @param array $parameters
	 * @param string $expectedType
	 * @param null|int $userId
	 * @throws \Exception
	 */
	public function checkRequestError($presenterName, $method = 'GET', $parameters = array(), $expectedType, $userId = NULL)
	{
		Assert::Error(function() use ($presenterName, $method, $parameters, $userId) {
			$this->sendRequest($presenterName, $method, $parameters, $userId);
		}, $expectedType);
	}
}

class Authenticator extends Object implements IAuthenticator
{

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @param array $credentials
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list( $id, $password ) = $credentials;
		return new Identity( $id, 'admin', NULL );
	}
}
