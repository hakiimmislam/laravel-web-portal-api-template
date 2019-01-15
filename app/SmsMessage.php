<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msisdn', 'purpose', 'sent'
    ];
}
