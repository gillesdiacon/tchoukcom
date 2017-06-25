<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    
    protected $with = ['subCategories', 'title', 'simpleProducts', 'variantProducts'];
    
    protected $table = 'category';

    public $timestamps = false;
	
	public function title() {
        global $languageId;
        return $this->hasOne('TcBern\Model\CategoryI18n')->language($languageId);
    }

    public function subCategories() {
        return $this->hasMany('TcBern\Model\Category','parent_id','id');
    }
    
    public function simpleProducts() {
        return $this->hasMany('TcBern\Model\Product','category_id','id')->simple();
    }
    
    public function variantProducts() {
        return $this->hasMany('TcBern\Model\Product','category_id','id')->variant();
    }
}
