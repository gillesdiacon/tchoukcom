<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model {
    
    protected $with = ['types'];

    protected $table = 'variant';

    public $timestamps = false;

    public function types(){
        return $this->hasMany('TcBern\Model\VariantType');
    }
}
