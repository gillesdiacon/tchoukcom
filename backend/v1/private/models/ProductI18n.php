<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class ProductI18n extends Model {

    protected $table = 'producti18n';

    public $timestamps = false;
    
    public function scopeLanguage($query, $languageId){
        return $query->where('language_id', $languageId);
    }
}
