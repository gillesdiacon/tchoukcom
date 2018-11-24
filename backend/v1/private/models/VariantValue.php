<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model {
    
    protected $with = ['name'];

    protected $table = 'variant_value';

    public $timestamps = false;

    public function name() {
        global $languageId;
        return $this->hasOne('TcBern\Model\VariantValueI18n')->language($languageId);
    }
}
