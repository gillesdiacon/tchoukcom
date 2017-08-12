<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class VariantValueI18n extends Model {

    protected $table = 'variant_value_i18n';

    public $timestamps = false;
    
    public function scopeLanguage($query, $languageId){
        return $query->where('language_id', $languageId);
    }
}
