<?php

namespace Helbrary\NetteTesterExtension;

use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Utils\Strings;
use Tester\Assert;


abstract class BasePresenterTester extends \Tester\TestCase
{

	const DEFAULT_USER_ROLE = 'admin';

	/**
	 * @var string
	 */
	protected $presenterName;

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

	/**
	 * BasePresenterTester constructor.
	 * @param string $presenterName  - etc. 'Front:GoodsChange:Goods'
	 */
	public function __construct($presenterName)
	{
		$this->presenterName = $presenterName;
		$this->container = require __DIR__ . '/../../../../app/bootstrap.php';
		$this->linkGenerator = $this->container->getByType('Nette\Application\LinkGenerator');
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
		$this->authenticator = new Authenticator();
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
	 * @param array $parameters
	 * @param string $method
	 * @param null|int $userId
	 * @param string $userRole
	 * @return \Nette\Application\IResponse
	 */
	public function sendRequest($parameters = array(), $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE)
	{
		$presenter = $this->getPresenter($this->presenterName);
		if ($userId !== NULL) {
			$presenter->user->setAuthenticator($this->authenticator);
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
	public function checkRequestNoError($parameters = array(), $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE)
	{
		Assert::noError(function() use ($method, $parameters, $userId, $userRole) {
			$response = $this->sendRequest($parameters, $method, $userId, $userRole);
			if ($response instanceof RedirectResponse) {
				throw new UnexpectedRedirectResponse();
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
		Assert::Error(function() use ($parameters, $method, $userId, $userRole) {
			$response = $this->sendRequest($parameters, $method, $userId, $userRole);
			if ($response instanceof RedirectResponse) {
				throw new UnexpectedRedirectResponse();
			}
		}, $expectedType);
	}

	/**
	 * @param array $parameters
	 * @param $redirectToAction - etc. 'Front:Sign:in'
	 * @param string $method
	 * @param int $userId
	 * @param string $userRole
	 * @param bool $ignoreRedirectUrlParameters
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function checkRedirectTo($parameters = array(), $redirectToAction, $method = 'GET', $userId = NULL, $userRole = self::DEFAULT_USER_ROLE, $ignoreRedirectUrlParameters = TRUE)
	{
		$response = $this->sendRequest($parameters, $method, $userId, $userRole);
		Assert::true($response instanceof \Nette\Application\Responses\RedirectResponse);
		if ($ignoreRedirectUrlParameters) {
			$responseUrl = $response->getUrl();
			$endPos = strrpos($responseUrl, '?');
			$responseUrlWithoutParameters = Strings::substring($responseUrl, 0, $endPos === FALSE ? NULL : $endPos);
			Assert::same($this->linkGenerator->link($redirectToAction), $responseUrlWithoutParameters);
		} else {
			Assert::same($this->linkGenerator->link($redirectToAction), $response->getUrl());
		}
	}

}
