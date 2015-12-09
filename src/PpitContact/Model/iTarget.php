<?php
namespace PpitContact\Model;

interface iTarget {

	public function __construct($message, $contactTable, $currentUser, $controller);
	public function loadData($data);
	public function loadDataFromRequest($request);
	public function compute();
	public function addTo($tel_cell);
}
