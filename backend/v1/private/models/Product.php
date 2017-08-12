<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    public $timestamps = false;
	
	public function title() {
        global $languageId;
        return $this->hasOne('TcBern\Model\ProductI18n')->language($languageId);
    }
    
    public function price(){
        global $priceListId;
        return $this->hasOne('TcBern\Model\Price')->priceList($priceListId);
    }
    
    public function variant(){
        return $this->hasOne('TcBern\Model\Variant','id','variant_id');
    }

    public function scopeSimple($query){
        return $query->where('variant_id', 0);
    }
    
    public function scopeVariant($query){
        return $query
            ->where('variant_id', '<>', 0)
            ->groupBy('variant_id');
    }
}
