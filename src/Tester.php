<?php

namespace Helbrary\NetteTesterExtension;

use Tester\Assert;
use Tester\TestCase;

abstract class Tester extends TestCase implements ITester
{

	/**
	 * Checks that the function does not generate PHP error and does not throw exception.
	 * @param callable $function
	 * @return void
	 */
	public function noError($function)
	{
		Assert::noError($function);
	}

	/**
	 * Checks that the function generate PHP error and does not throw exception.
	 * @param callable $function
	 * @param string $expectedType
	 * @param null|string $expectedMessage
	 * @return void
	 */
	public function error($function, $expectedType, $expectedMessage = NULL)
	{
		Assert::error($function, $expectedType, $expectedMessage = NULL);
	}

	/**
	 * Checks assertion. Values must be true.
	 * @param mixed $actual
	 * @return void
	 */
	public function assertTrue($actual)
	{
		Assert::true($actual);
	}

	/**
	 * Checks assertion. Values must be exactly the same.
	 * @param mixed $expected
	 * @param mixed $actual
	 * @return void
	 */
	public function assertSame($expected, $actual)
	{
		Assert::same($expected, $actual);
	}
}