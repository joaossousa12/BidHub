<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Auction extends Model
{

    public $timestamps  = false;

    protected $table = 'auction';

    protected $dates = ['datecreated']; 
    protected $casts = [
        'datecreated' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'description',
        'minvalue',
        'starting_value',
        'duration',
        'datecreated',
        'owner_id',
        'state_id',
            ];


    public function user() {
        return $this->belongsTo('App\Models\User','owner_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(AuctionState::class, 'state_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_name');
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'belongs',       // Pivot table name
            'auction_id',    // Foreign key on the pivot table for the Auction model
            'categoryname',  // Foreign key on the pivot table for the Category model
            null,            // Override the default key name if it's not 'id'
            'categoryname'   // Local key in the Category model
        );
    }

    /*
    public function extendDuration($additionalMinutes)
    {
        $endDate = Carbon::parse($this->datecreated)->addSeconds($this->duration * 86400);
        $newEndDate = $endDate->addMinutes($additionalMinutes);
        $this->duration = ($newEndDate->diffInSeconds($this->datecreated)) / 86400; // Atualiza a duração em dias
        $this->save();
    }
    */
}