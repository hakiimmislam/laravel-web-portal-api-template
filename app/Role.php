<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];

    /**
     * Relationships
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    public function submenus()
    {
    	return $this->belongsToMany('App\Submenu');
    }
    public function menus()
    {
        return $this->belongsToMany('App\Menu');
    }
}
