<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_id', 'name', 'slug', 'icon', 'order'];

    /**
     * Relationships
     */
    public function menu()
    {
        return $this->belongsTo('App\Menu');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role')->withTimestamps();
    }
}
