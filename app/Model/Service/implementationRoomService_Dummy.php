<?php

/**
 * Created by PhpStorm.
 * User: eliottvincent
 * Date: 08/06/2017
 * Time: 15:27
 */
class implementationRoomService_Dummy implements interfaceRoomService
{

	//================================================================================
	// properties
	//================================================================================

	/**
	 * @var null
	 */
	private static $_instance = null;

	private $_roomDAO;
	private $_rooms = array();
	private $_sessionRooms = null;
	private $_xmlRooms;


	//================================================================================
	// constructor and initialization
	//================================================================================

	/**
	 * Constructeur de la classe
	 *
	 * @param void
	 * @return void
	 */
	private function __construct() {

		// instantiating the DAOs we need
		$this->_roomDAO = implementationRoomDAO_Dummy::getInstance();

		// instantiating the services we need
		$this->_doorService = implementationDoorService_Dummy::getInstance();
		$this->_lockService = implementationLockService_Dummy::getInstance();
		$this->_keyService = implementationKeyService_Dummy::getInstance();
		$this->_userService = implementationUserService_Dummy::getInstance();

		// getting the data we need
		$this->_xmlRooms = $this->_roomDAO->getRooms();
		if (isset($_SESSION["ROOMS"])) {
			$this->_sessionRooms = $_SESSION["ROOMS"];
		}

		// if we got doors in session
		if ($this->_sessionRooms !== null) {

			$this->_rooms = $this->_sessionRooms;
		}

		// else that means there are no doors in session (first use)
		else {

			$_SESSION["ROOMS"] = $this->_xmlRooms;
			$this->_rooms = $this->_xmlRooms;
			$this->_sessionRooms = $this->_xmlRooms;
		}
	}

	/**
	 * Méthode qui crée l'unique instance de la classe
	 * si elle n'existe pas encore puis la retourne.
	 *
	 * @param void
	 * @return implementationRoomService_Dummy
	 */
	public static function getInstance() {

		if(is_null(self::$_instance)) {
			self::$_instance = new implementationRoomService_Dummy();
		}

		return self::$_instance;
	}

	//================================================================================
	// Getters
	//================================================================================

	public function getRooms()
	{
		return $this->_rooms;
	}

	public function getRoom($id)
	{

		foreach ($this->_rooms as $room) {
			if ($room->getId() == (string) $id) {
				return $room;
			}
		}
	}


	//================================================================================
	// CREATE
	//================================================================================

	public function saveRoom($roomArray)
	{

		$roomToSave = new RoomVO();
		$roomToSave->setId((string) $roomArray['room_id']);
		$roomToSave->setName((string) $roomArray['room_name']);
		$roomToSave->setBuilding((string) $roomArray['room_building']);
		$roomToSave->setFloor((string) $roomArray['room_floor']);

		array_push($_SESSION["ROOMS"], $roomToSave);
		array_push($this->_rooms, $roomToSave);
		array_push($this->_sessionRooms, $roomToSave);

	}


	//================================================================================
	// DELETE
	//================================================================================

	public function deleteRoom($id) {

		$this->updateServiceVariables();

		foreach ($this->_rooms as $key=>$room) {

			if ($room->getId() == (string) $id) {

				unset($_SESSION["ROOMS"][$key]);
				unset($this->_sessionRooms[$key]);
				unset($this->_rooms[$key]);

				return true;
			}
		}

		return false;
	}


	//================================================================================
	// UPDATE
	//================================================================================

	public function updateRoom($roomArray) {

		$roomToUpdate = new RoomVO();
		$roomToUpdate->setId((string) $roomArray['room_id']);
		$roomToUpdate->setName((string) $roomArray['room_name']);
		$roomToUpdate->setBuilding((string) $roomArray['room_building']);
		$roomToUpdate->setFloor((string) $roomArray['room_floor']);
		$roomToUpdate->setDoors((array) $roomArray['room_doors']);

		foreach ($this->_rooms as $key=>$room) {

			if ($room->getId() == $roomToUpdate->getId()) {

				$_SESSION["ROOMS"][$key] = $roomToUpdate;
				$this->_sessionRooms[$key] = $roomToUpdate;
				$this->_rooms[$key] = $roomToUpdate;

				return true;
			}

		}

		return false;
	}

	//================================================================================
	// SPECIFIC GETTERS
	//================================================================================

	/**
	 * @param $room
	 */
	function getRoomKeys($room) {

		// getting $doors ids concerned by the room
		$doorsIds = $room->getDoors();

		// getting all the locks concerned by any of the room's doors
		$locks = array();
		$allLocks = $this->getLocks();
		foreach ($allLocks as $oneLock) {

			foreach ($doorsIds as $doorId) {

				if ($doorId == $oneLock->getDoor()) {

					array_push($locks, $oneLock);
				}
			}
		}

		// getting all the keys concerned by any of the room's locks
		$keys = array();
		$allKeys = $this->getKeys();
		foreach ($allKeys as $oneKey) {

			foreach ($locks as $lock) {

				foreach ($oneKey->getLocks() as $keyLockId) {

					if ($lock->getId() == $keyLockId) {

						array_push($keys, $oneKey);
					}
				}
			}
		}

		return $keys;
	}

	/**
	 * @param $room
	 */
	function getRoomUsers($room) {

		$allUsers = $this->getUsers();

		// getting all the keys concered by the room
		$keys = $this->getRoomKeys($room);

		// then we need to check if there are some borrowings concerned by these keys
		return $allUsers;
	}

	//================================================================================
	// OTHER
	//================================================================================

	public function checkUnicity($id)
	{
		if ($this->_rooms) {

			foreach ($this->_rooms as $room) {

				if ($room->getId() == (string) $id) {

					return true;
				}
			}
		}

		return false;
	}

	private function updateServiceVariables() {

		if (isset($_SESSION["ROOMS"])) {
			$this->_sessionRooms = $_SESSION["ROOMS"];
			$this->_rooms = $_SESSION["ROOMS"];
		}

	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function getDoor($id) {

		return $this->_doorService->getDoor($id);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function getLocks() {

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
	 * @param $id
	 * @return mixed
	 */
	private function getKeys() {

		return $this->_keyService->getKeys();
	}

	/**
	 * To get all users.
	 * @return null
	 */
	private function getUsers() {

		return $this->_userService->getUsers();
	}

}
