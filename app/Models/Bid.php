<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    public $timestamps = false;

    protected $table = 'bid';
    protected $primaryKey = ['auction_id', 'bidder'];
    public $incrementing = false;

    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'bidder', 'id');
    }

    public function auction()
    {
        return $this->belongsTo('App\Models\Auction', 'auction_id', 'id');
    }
}