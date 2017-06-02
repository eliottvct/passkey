<?php
class LockVO
{
	public static $locksList = array();

    private $id;
    private $name;
    private $door; // the door ID

	public function __construct() {
	}

    // GETTER
    public function getId() {
        return $this->id;
    }

	public function getName() {
		return $this->name;
	}

	public function getDoor() {
    	return $this->door;
	}

    // SETTER
	public function setId($id) {
		$this->id = $id;
	}

    public function setName($name) {
        $this->name = $name;
    }

    public function setDoor($door) {
    	$this->door = $door;
	}
}
