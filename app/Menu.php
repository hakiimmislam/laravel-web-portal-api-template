<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'icon', 'order'];

    /**
     * Relationships
     */
    public function submenus()
    {
    	return $this->hasMany('App\Submenu');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role')->withTimestamps();
    }
}
