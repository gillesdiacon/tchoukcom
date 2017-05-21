<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    protected $table = 'category';

    public $timestamps = false;
	
	public function title() {
        return $this->hasOne('TcBern\Model\CategoryI18n');
    }

    public function subCategories() {
        return $this->hasMany('TcBern\Model\Category','parent_id','id') ;
    }
}
