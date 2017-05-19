<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'identity';

    // 'id', 'created_at' and 'updated_at' columns are automatically added by Eloquent

    public function user(){
        return $this->belongsTo('TcBern\\Model\\User', 'user_id');
    }
}