<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    // 'id', 'created_at' and 'updated_at' columns are automatically added by Eloquent

    public function groups() {
        return $this->belongsToMany('TcBern\\Model\\Group', 'usergroup');
    }
}
