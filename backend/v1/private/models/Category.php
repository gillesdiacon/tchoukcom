<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    
    protected $with = ['subCategories', 'title'];
    
    protected $table = 'category';

    public $timestamps = false;
	
	public function title() {
        global $languageId;
        return $this->hasOne('TcBern\Model\CategoryI18n')->language($languageId);
    }

    public function subCategories() {
        return $this->hasMany('TcBern\Model\Category','parent_id','id') ;
    }
}
