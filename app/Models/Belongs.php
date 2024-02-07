<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Belongs extends Model
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
    protected $table = 'belongs';

    protected $primaryKey = 'auction_id';
}