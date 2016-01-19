# Nette tester extension

Easy testing of presenters

For each presenter to test must be created separated class

```sh
<?php

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../../vendor/helbrary/nette-tester-extension/src/BasePresenterTester.php';

final class TestAccountPresenter extends \Helbrary\NetteTesterExtension\BasePresenterTester
{

	/**
	 * @var \Model\UserModel
	 */
	private $userModel;

	/**
	 * @var bool|\Model\User
	 */
	private $testingUser;

	/**
	 * TestGoodsPresenter constructor.
	 */
	public function __construct()
	{
		parent::__construct('Front:Account'); // Here is defined presenter which we want test
		$this->userModel = $this->container->getByType('\Model\UserModel');
		$this->testingUser = $this->userModel->find()->fetch(); // get some user from db
	}

	/**
	 * Test detail action of account presenter
	 */
	public function testDetail()
	{
		// if no user is logged in, expected redirect to Sign in
		$this->checkRedirectTo(array(
			'action' => 'detail',
		), 'Front:Sign:in');

		// if some user is logged in, expected that action detail will be render without error
		// after send this request is logged in user with id $this->testingUser->id
		$this->checkRequestNoError(array(
			'action' => 'detail',
		), 'GET', $this->testingUser->id);
	}
}

$testCase = new TestAccountPresenter();
$testCase->run();
```
