<?php

namespace Helbrary\NetteTesterExtension;

interface ITester
{
	/**
	 * Checks that the function does not generate PHP error and does not throw exception.
	 * @param callable $function
	 * @return void
	 */
	public function noError($function);

	/**
	 * Checks that the function generate PHP error and does not throw exception.
	 * @param callable $function
	 * @param string $expectedType
	 * @param null|string $expectedMessage
	 * @return void
	 */
	public function error($function, $expectedType, $expectedMessage = NULL);

	/**
	 * Checks assertion. Values must be true.
	 * @param bool $actual
	 * @return void
	 */
	public function assertTrue($actual);

	/**
	 * Checks assertion. Values must be exactly the same.
	 * @param mixed $expected
	 * @param mixed $actual
	 * @return void
	 */
	public function assertSame($expected, $actual);
}