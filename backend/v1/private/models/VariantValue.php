<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model {
    
    protected $with = ['name', 'productVariantValue'];

    protected $table = 'variant_value';

    public $timestamps = false;

    public function name() {
        global $languageId;
        return $this->hasOne('TcBern\Model\VariantValueI18n')->language($languageId);
    }
    
    public function productVariantValue(){
        return $this->hasOne('TcBern\Model\ProductVariantValue','variant_value_id','id');
    }
}
