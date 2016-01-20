# Nette tester extension

Easy testing of presenters

Library contains class BasePresenterTester which provide following methods:

- **checkRequestNoError** - expect that some action of presenter is without error
- **checkRequestError** - expect that some action of presenter throw error
- **checkRedirectTo** - expect that some action of presenter return redirect

# Authenticator

In default ever request are sent as from unlogged user. If we want test behavior of action when is some user logged in, we can use parameters "*$userId*" etc. which provide all check* methods. We do not need to know password of user. Simply say that we want be logged in as user with id "*$userId*".

Default role of logged user is '*admin*'. For change role use parameter "*$userRole*"

# Example in practice

**!!! Important recomended: Before start tests clear cache !!!**

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
			'id' => 8, // parameter id for action detail
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
