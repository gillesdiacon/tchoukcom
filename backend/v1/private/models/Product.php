<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    public $timestamps = false;
	
	public function title() {
        global $languageId;
        return $this->hasOne('TcBern\Model\ProductI18n')->language($languageId);
    }

    public function scopeSimple($query){
        return $query->where('variant_group_id', 0);
    }
    
    public function scopeVariant($query){
        return $query
            ->where('variant_group_id', '<>', 0)
            ->groupBy('variant_group_id');
    }
}
