<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category';

    protected $primaryKey = 'categoryname';  // Indicates that the primary key is not auto-incrementing
    protected $keyType = 'string';
    public $incrementing = false;

    public function auctions()
    {
        return $this->belongsToMany(
            Auction::class,
            'belongs',
            'categoryname',
            'auction_id'
        );
    }
}