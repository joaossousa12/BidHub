<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class User extends Authenticatable
{
    use Notifiable;


    public $timestamps = false;

    protected $fillable = [
        'username', 
        'email', 
        'password',
        'address',
        'postalcode',
        'phonenumber',
        'is_admin',
        'profile_picture',
        'is_deleted',
        'is_banned',
        'credit',
        'average_rating',
        'rating_count'
    ];


    protected $hidden = [
        'password'
    ];


    public function auctions()
    {
        return $this->hasMany('App\Auction', 'id', 'owner_id');
    }

    public function bids()
    {
        return $this->hasMany('App\Bid', 'id', 'idbuyer');
    }
    
    public function followingAuctions()
    {
        return $this->belongsToMany(Auction::class, 'follows', 'buyer_id', 'auction_id');
    }

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_picture');
        });
    }


}