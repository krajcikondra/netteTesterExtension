<?php

namespace Helbrary\NetteTesterExtension;

use Tester\Assert;

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

	public function __construct()
	{
		$this->container = require __DIR__ . '/../../../../app/bootstrap.php';
		$this->linkGenerator = $this->container->getByType('Nette\Application\LinkGenerator');
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
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
	 * @return \Nette\Application\IResponse
	 */
	public function sendRequest($presenterName, $method = 'GET', $parameters = array())
	{
		$presenter = $this->getPresenter($presenterName);
		$request = new \Nette\Application\Request($presenterName, $method, $parameters);
		/** @var $response \Nette\Application\Responses\RedirectResponse */
		return $presenter->run($request);
	}

	/**
	 * Check if request is without error
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $method
	 * @param array $parameters
	 */
	public function checkRequestNoError($presenterName, $method = 'GET', $parameters = array())
	{
		Assert::noError(function() use ($presenterName, $method, $parameters) {
			$this->sendRequest($presenterName, $method, $parameters);
		});
	}

	/**
	 * Check if request is without error
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $method
	 * @param array $parameters
	 * @param string $expectedType
	 * @throws \Exception
	 */
	public function checkRequestError($presenterName, $method = 'GET', $parameters = array(), $expectedType)
	{
		Assert::Error(function() use ($presenterName, $method, $parameters) {
			$this->sendRequest($presenterName, $method, $parameters);
		}, $expectedType);
	}
}
