<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class VariantTypeI18n extends Model {

    protected $table = 'variant_type_i18n';

    public $timestamps = false;
    
    public function scopeLanguage($query, $languageId){
        return $query->where('language_id', $languageId);
    }
}
