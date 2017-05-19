<?php namespace TcBern\Model;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'group';

    // 'id', 'created_at' and 'updated_at' columns are automatically added by Eloquent

    public function users() {
        return $this->belongsToMany('TcBern\\Model\\User', 'usergroup');
    }
}
