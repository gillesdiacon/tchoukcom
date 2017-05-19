<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'profile';

    // 'id', 'created_at' and 'updated_at' columns are automatically added by Eloquent

    public function identity(){
        return $this->belongsTo('TcBern\\Model\\Identity', 'identity_id');
    }

    public function positions() {
        return $this->belongsToMany('TcBern\\Model\\Position', 'profileposition');
    }
}