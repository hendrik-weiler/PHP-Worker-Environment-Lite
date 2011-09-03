<?php

class edbtest
{
	private $edb;
	
	public function startup()
	{
		$this->edb = new eDB('.');
		$x = new eDB_Select('translation_de','*');
		
		var_dump($x);
	}
	
	public function insert()
	{
		$insert = new eDB_Insert('translation_de',array(
			'keyword' => 'test',
			'translation' => 'dies ist ein test!'
		));
		
	}
	
	public function delete()
	{
		$delete = new eDB_Delete('translation_de',array(
			'id' => 7,
			'keyword' => 'test'
		));
	}
	
	public function update()
	{
		$update = new eDB_Update('translation_de',array(
			'keyword' => 'test'
		),array(
			'translation' => 'Ich wurde geändert'
		));
	}
	
	public function index()
	{
		
	}
}
