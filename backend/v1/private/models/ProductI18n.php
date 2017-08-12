<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class ProductI18n extends Model {

    protected $table = 'product_i18n';

    public $timestamps = false;
    
    public function scopeLanguage($query, $languageId){
        return $query->where('language_id', $languageId);
    }
}
