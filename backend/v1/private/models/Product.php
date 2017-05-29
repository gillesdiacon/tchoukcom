<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    public $timestamps = false;
	
	public function title() {
        return $this->hasOne('TcBern\Model\ProductI18n');
    }
}
