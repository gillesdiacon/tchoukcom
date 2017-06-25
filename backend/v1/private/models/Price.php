<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Price extends Model {

    protected $table = 'price';
    
    // protected $casts = [
        // 'price' => 'float(11,2)'
    // ];

    public $timestamps = false;
    
    public function scopePriceList($query, $priceListId){
        return $query->where('price_list_id', $priceListId);
    }
}
