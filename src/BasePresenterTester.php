<?php

namespace Helbrary\NetteTesterExtension;

use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Security\IAuthenticator;
use Nette\Utils\Strings;


abstract class BasePresenterTester extends Tester
{

	const DEFAULT_USER_ROLE = 'admin';

	/** @var string */
	protected $presenterName;

	/** @var \Nette\DI\Container */
	protected $container;

	/** @var \Nette\Application\LinkGenerator */
	protected $linkGenerator;

	/** @var \Nette\Application\IPresenterFactory */
	protected $presenterFactory;

	/** @var  IAuthenticator */
	protected $authenticator;

	/** @var string */
	protected $userStorageNamespace;

	/**
	 * BasePresenterTester constructor.
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $bootstrapPath
	 */
	public function __construct($presenterName, $bootstrapPath = __DIR__ . '/../../../../app/bootstrap.php')
	{
		$this->presenterName = $presenterName;
		$this->container = require $bootstrapPath;
		$this->linkGenerator = $this->container->getByType('Nette\Application\LinkGenerator');
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
		$this->authenticator = $this->container->getByType(IAuthenticator::class);
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
	 * @param string $namespace
	 */
	public function setUserStorageNamespace($namespace)
	{
		$this->userStorageNamespace = $namespace;
	}

	/**
	 * Create and send request
	 * @param array       $parameters
	 * @param string      $method
	 * @param null|int    $userId
	 * @param string      $userRole
	 * @param null|array  $identityData
	 * @return \Nette\Application\IResponse
	 */
	public function sendRequest($parameters = array(), $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE, $identityData = NULL)
	{
		$presenter = $this->getPresenter($this->presenterName);
		if ($userId !== NULL) {
			$presenter->user->setAuthenticator($this->authenticator);
			$this->authenticator->setIdentityData($identityData);
			if ($this->userStorageNamespace) {
				$presenter->user->getStorage()->setNamespace($this->userStorageNamespace);
			}
			$presenter->user->login($userId, $userRole);

		} else {
			$presenter->user->logOut();
		}
		$request = new \Nette\Application\Request($this->presenterName, $method, $parameters);
		$response = $presenter->run($request);

		if ($response instanceof TextResponse) {
			if ($response->getSource() instanceof \Nette\Application\UI\ITemplate) {
				$output = $response->getSource()->render();
			}
		}
		return $response;
	}

	/**
	 * Check if request is without error
	 * @param array $parameters
	 * @param string $method
	 * @param null|int $userId
	 * @param string $userRole
	 * @throws UnexpectedRedirectResponse
	 */
	public function checkRequestNoError($parameters = array(), $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE, $identityData = NULL)
	{
		$this->noError(function() use ($method, $parameters, $userId, $userRole, $identityData) {
			$response = $this->sendRequest($parameters, $method, $userId, $userRole, $identityData);
			if ($response instanceof RedirectResponse) {
				throw new UnexpectedRedirectResponse($response->getUrl());
			}
		});
	}

	/**
	 * Check if request is without error
	 * @param array $parameters
	 * @param string $expectedType
	 * @param string $method
	 * @param null|int $userId
	 * @param string $userRole
	 * @throws UnexpectedRedirectResponse
	 */
	public function checkRequestError($parameters = array(), $expectedType, $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE)
	{
		$this->error(function() use ($parameters, $method, $userId, $userRole) {
			$response = $this->sendRequest($parameters, $method, $userId, $userRole);
			if ($response instanceof RedirectResponse) {
				throw new UnexpectedRedirectResponse();
			}
		}, $expectedType);
	}

	/**
	 * @param array  $parameters
	 * @param        $redirectToAction - etc. 'Front:Sign:in'
	 * @param string $method
	 * @param int    $userId
	 * @param string $userRole
	 * @param bool   $ignoreRedirectUrlParameters
	 * @param array|null   $identityData
	 */
	public function checkRedirectTo($parameters = array(), $redirectToAction, $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE, $ignoreRedirectUrlParameters = TRUE, $identityData = NULL)
	{
		$response = $this->sendRequest($parameters, $method, $userId, $userRole, $identityData);
		$this->assertTrue($response instanceof \Nette\Application\Responses\RedirectResponse);
		if ($ignoreRedirectUrlParameters) {
			$responseUrl = $response->getUrl();
			$endPos = strrpos($responseUrl, '?');
			$responseUrlWithoutParameters = Strings::substring($responseUrl, 0, $endPos === FALSE ? NULL : $endPos);
			$this->assertSame($this->linkGenerator->link($redirectToAction), $responseUrlWithoutParameters);
		} else {
			$this->assertSame($this->linkGenerator->link($redirectToAction), $response->getUrl());
		}
	}

}
