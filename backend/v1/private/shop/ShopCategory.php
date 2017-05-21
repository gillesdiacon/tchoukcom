<?php namespace TcBern\Shop;

use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'category';

    public $timestamps = false;
}
