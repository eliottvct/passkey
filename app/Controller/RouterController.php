<?php

/**
 * Created by PhpStorm.
 * User: eliottvincent
 * Date: 04/05/2017
 * Time: 15:07
 */


class RouterController extends Controller
{

	function dispatchRoute() {

		$model = new Model();
		$controller = new Controller($model);

		// handling requests on http://passkey.enssat/?action=some_action
		if (isset($_GET['action']) && !empty($_GET['action'])) {

			// nice trick to call $controller->name_of_action(), or any other action, programmatically
			// TODO : check if the function does exist and is defined
			$this->{$_GET['action']}();
		}

		// handling requests on http://passkey.enssat/
		else if (isset($_REQUEST['url']) && $_REQUEST['url'] === '') {
			//echo $this->createBlankPage($controller, $model)->render();
			$this->createBlankPage($controller, $model);
		}

		// handling requests on http://passkey.enssat/something_else
		else {
			echo 'I received a ' . (($_SERVER['REQUEST_METHOD'] === 'GET') ? 'GET' : 'POST') . ' request on "/' . $_REQUEST['url'] . '", what should I do?';
			$this->{$_REQUEST['url']}();
		}
	}

	function showLoginPageTest() {

		echo $this->createLoginPage()->oldRenderMethod();
	}

	function login() {
		$authentificationController = new AuthentificationController();
		$authentificationController->login();
	}

	function logout() {
		$authentificationController = new AuthentificationController();
		$authentificationController->logout();
	}

	function createDoor() {
		new CreateDoorController();
	}

	function createLock() {
		new CreateLockController();
	}

	function createKey() {
		new CreateKeyController();
	}

	function listKeys() {
		new ListKeysController();
	}


	/**
	 * Creates a blank page as a CompositeView
	 *
	 * @param $controller
	 * @param $model
	 * @return CompositeView
	 */
	function createBlankPage($controller, $model) {

		// authentication check
		$authentificationController = new AuthentificationController();
		$authentificationController->check();

		// creating a default CompositeView
		$compositeView = new CompositeView(true);

		// creating our content, as a View object
		$blankContent = new View(null, null, 'default_content.html.twig');

		// adding the content to our CompositeView
		// here we use attachContentView() rather than attachView()...
		// because the content view always needs to be between content_start and content_end
		$compositeView->attachContentView($blankContent);

		echo $compositeView->render();
	}

	function createLoginPage() {
		$html = new View(null, null,'partials/page_user_login_1.php');

		return $html;
	}
}
