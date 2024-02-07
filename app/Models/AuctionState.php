<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionState extends Model
{
    protected $table = 'auction_state';

    protected $fillable = ['state_name'];

    public function auctions()
    {
        return $this->hasMany(Auction::class, 'state_id');
    }

    public function state()
    {
        return $this->belongsTo(AuctionState::class, 'auction_state_id');
    }

}