<?php


class LockController {


	//================================================================================
	// constructor
	//================================================================================

	/**
	 * LockController constructor.
	 */
	public function __construct() {
		$this->_lockService = implementationLockService_Dummy::getInstance();
		$this->_doorService = implementationDoorService_Dummy::getInstance();
	}


	//================================================================================
	// LIST
	//================================================================================

	/**
	 *  used to list all locks
	 */
	public function list() {

		$locks = $this->getLocks();

		if (!empty($locks)) {
			$this->displayList(true);
		}
		else {
			$alert['type'] = 'danger';
			$alert['message'] = 'Nous n\'avons aucun canon d\'enregistré.';
			$alerts[] = $alert;
			$this->displayList(false, $alerts);
		}
	}

	/**
	 * @param $state
	 * @param null $messages
	 */
	public function displayList($state, $messages = null) {
		if ($state) {
			$locks = LockController::getLocks();
		} else {
			$locks = null;
		}
		$composite = new CompositeView(
			true,
			'Liste des canons',
			'Cette page permet de modifier et/ou supprimer des canons.',
			"lock",
			array("sweetAlert" => "https://cdn.jsdelivr.net/sweetalert2/6.6.2/sweetalert2.min.css"),
			array("deleteLockScript" => "app/View/assets/custom/scripts/deleteLock.js",
				"sweetAlert" => "https://cdn.jsdelivr.net/sweetalert2/6.6.2/sweetalert2.min.js"));

		if ($messages != null) {
			foreach ($messages as $message) {
				if (!empty($message['type']) && !empty($message['message'])) {
					$submit_message = new View("submit_message.html.twig", array("alert_type" => $message['type'] , "alert_message" => $message['message']));
					$composite->attachContentView($submit_message);
				}
			}
		}
		$list_locks = new View("locks/list_locks.html.twig", array('locks' => $locks));
		$composite->attachContentView($list_locks);

		echo $composite->render();
	}


	//================================================================================
	// CREATE
	//================================================================================

	/**
	 * To create a lock.
	 */
	public function create() {

		// if no values are posted
		if (!isset($_POST['lock_name']) &&
			!isset($_POST['lock_door'])) {

			$this->displayForm();

		}

		// if some (but not all) values are posted
		elseif (empty($_POST['lock_name']) ||
			empty($_POST['lock_door'])) {

			$m_type = "danger";
			$m_message = "Toutes les valeurs nécessaires n'ont pas été trouvées. Merci de compléter tous les champs.";
			$message['type'] = $m_type;
			$message['message'] = $m_message;

			$this->displayForm(array($message));
		}

		// if we have all values, we can create a lock
		else {

			// id generation
			$id = 'l_' . strtolower(str_replace(' ', '_', addslashes($_POST['lock_name'])));

			// unicity check
			$exist = $this->checkUnicity($id);

			if (!$exist) {
				$lockToSave = array(
					'lock_id' => $id,
					'lock_name' => addslashes($_POST['lock_name']),
					'lock_door' => addslashes($_POST['lock_door'])
				);

				$this->saveLock($lockToSave);

				$m_type = "success";
				$m_message = "Le canon a bien été enregistré.";
				$message['type'] = $m_type;
				$message['message'] = $m_message;

				$this->displayForm(array($message));
			}
			else {
				$m_type = "danger";
				$m_message = "Un canon avec le même nom existe déjà.";
				$message['type'] = $m_type;
				$message['message'] = $m_message;

				$this->displayForm(array($message));
			}
		}
	}

	/**
	 * To display the form used to create lock.
	 * @param null $message array The type and the text of the message
	 */
	public function displayForm($messages = null) {

		$doors = $this->getDoors();

		$compositeView = new CompositeView(
			true,
			'Ajouter un canon',
			null,
			"lock");

		if ($messages != null) {
			foreach ($messages as $message) {
				if (!empty($message['type']) && !empty($message['message'])) {
					$message = new View("submit_message.html.twig", array("alert_type" => $message['type'] , "alert_message" => $message['message']));
					$compositeView->attachContentView($message);
				}
			}
		}

		$create_lock = new View("locks/create_lock.html.twig", array('doors' => $doors, 'previousUrl' => getPreviousUrl()));
		$compositeView->attachContentView($create_lock);

		echo $compositeView->render();
	}


	//================================================================================
	// DELETE
	//================================================================================

	/**
	 *
	 */
	public function deleteLockAjax() {

		session_start();

		if (isset($_POST['value'])) {

			if ($this->deleteLock($_POST['value']) == true) {
				$response['locks'] = $this->getLocks();
				$response['status'] = 'success';
				$response['message'] = 'This was successful';
			}
			else {
				$response['status'] = 'error';
				$response['message'] = 'This failed';
			}
		}
		else {
			$response['status'] = 'error';
			$response['message'] = 'This failed';
		}

		echo json_encode($response);
	}


	//================================================================================
	// UPDATE
	//================================================================================

	/**
	 *
	 */
	public function update() {

		if (isset($_POST['update']) && !empty($_POST['update'])) {
			$lock = $this->getLock($_POST['update']);
			$this->displayUpdateForm($lock);
		}

		// if all values were posted (= form submission)
		elseif (isset($_POST['lock_name']) &&
			isset($_POST['lock_door'])) {

			$lockToUpdate = array(
				'lock_id' => addslashes($_POST['lock_id']),
				'lock_name' => addslashes($_POST['lock_name']),
				'lock_door' => addslashes($_POST['lock_door'])
			);

			if ($this->updateLock($lockToUpdate) == false) {
				$message['type'] = 'danger';
				$message['message'] = 'Erreur lors de la modification du cannon.';
				$this->displayList(true, array($message));
			}
			else {
				$message['type'] = 'success';
				$message['message'] = 'Le canon a bien été modifié.';
				$this->displayList(true, array($message));
			}
		}

		else {

			$locks = $this->getLocks();

			if (!empty($locks)) {
				$this->displayList(true);
			}
			else {
				$message['type'] = 'danger';
				$message['message'] = 'Nous n\'avons aucun canon d\'enregistré.';
				$this->displayList(false, array($message));
			}
		}
	}

	/**
	 * @param $lock
	 * @param null $messages
	 */
	public function displayUpdateForm($lock, $messages = null) {

		$doors = $this->getDoors();

		$compositeView = new CompositeView(
			true,
			'Mettre à jour un canon',
			null,
			"lock");

		if ($messages != null) {
			foreach ($messages as $message) {
				if (!empty($message['type']) && !empty($message['message'])) {
					$message = new View("submit_message.html.twig", array("alert_type" => $message['type'] , "alert_message" => $message['message']));
					$compositeView->attachContentView($message);
				}
			}
		}

		$update_lock = new View('locks/update_lock.html.twig', array('lock' => $lock, 'doors' => $doors, 'previousUrl' => getPreviousUrl()));
		$compositeView->attachContentView($update_lock);

		echo $compositeView->render();
	}






	//================================================================================
	// calls to Service
	//================================================================================

	/**
	 * To get all locks
	 * @return null
	 */
	public function getLocks() {

		return $this->_lockService->getLocks();
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function getLock($id) {

		return $this->_lockService->getLock($id);
	}

	/**
	 * To get all doors
	 * @return array
	 */
	public function getDoors() {

		return $this->_doorService->getDoors();
	}

	/**
	 * @param $lockToSave
	 */
	private function saveLock($lockToSave) {

		$this->_lockService->saveLock($lockToSave);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function deleteLock($id) {

		return $this->_lockService->deleteLock($id);
	}

	/**
	 * @param $lockToUpdate
	 */
	private function updateLock($lockToUpdate) {

		return $this->_lockService->updateLock($lockToUpdate);
	}

	/**
	 * @param $id
	 * @return bool
	 */
	private function checkUnicity($id) {

		return $this->_lockService->checkUnicity($id);
	}

}
