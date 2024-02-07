<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $table = 'notification';
    
    public $timestamps = false; // Add this line to disable timestamps

    protected $fillable = [
        'user_id',
        'auction_id',
        'viewed',
        'date',
        'information'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'bidder', 'id');
    }

    public function auction()
    {
        return $this->belongsTo('App\Models\Auction', 'auction_id', 'id');
    }
}