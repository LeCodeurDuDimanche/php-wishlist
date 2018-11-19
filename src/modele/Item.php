<?php
class Item extends \Illuminate\Database\Eloquent\Model{
	protected $table = 'Item';
	protected $primaryKey = 'id';
	public $timestamps = false;
}