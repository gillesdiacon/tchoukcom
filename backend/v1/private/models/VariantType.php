<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class VariantType extends Model {
    
    protected $with = ['name','values'];

    protected $table = 'variant_type';

    public $timestamps = false;

    public function name() {
        global $languageId;
        return $this->hasOne('TcBern\Model\VariantTypeI18n')->language($languageId);
    }
    
    public function values(){
        return $this->hasMany('TcBern\Model\VariantValue');
    }
    
    public function other_values($excludeVariantValueId){
        return $this->hasMany('TcBern\Model\VariantValue')->where('id', '!=', $excludeVariantValueId);
    }
}
