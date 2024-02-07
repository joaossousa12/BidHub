<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Bid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class BidController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getMaxBid(Request $request)
    {
        try {
            if (!($request->ajax() || $request->pjax())) {
                return response('Forbidden.', 403);
            }

            $auctionID = $request->input('auctionID');
            $query = "SELECT max(bidValue) FROM bid WHERE auction_id = ?";
            $response = DB::select($query, [$auctionID]);
            if ($response[0]->max == null) {
                $response[0]->max = 0.00;
            }
        } catch (Exception $e) {
            $this->error($e);
            return response('Internal Error', 500);
        }

        return response()->json($response[0]);
    }

    public static function getMaxBidInternal($id)
    {
        $query = "SELECT max(bidValue) FROM bid WHERE auction_id = ?";
        $response = DB::select($query, [$id]);
        if ($response[0]->max == null) {
            $response[0]->max = 0.00;
        }

        return $response[0]->max;
    }

    public function store(Request $request)
    {
        $request->validate([
            'auction_id' => 'required|exists:auction,id', // Make sure table name is 'auction'
            'bid_amount' => 'required|numeric|min:0', // Adjust the validation as needed
        ]);

        $auctionId = $request->auction_id;
        $bidAmount = $request->bid_amount;

        $bidderId = auth()->id();
        $bidder = User::findOrFail($bidderId); 
        $bidDate = now();

        if ($bidder->credit < $bidAmount) {
            return redirect()->back()->withErrors(['You do not have enough credits to place this bid.']);
        }

        $maxBid = DB::table('bid')
                    ->where('auction_id', $auctionId)
                    ->max('bidvalue');

        if ($maxBid) {
            $currentMaxBidder = DB::table('bid')
                                ->where('auction_id', $auctionId)
                                ->where('bidvalue', $maxBid)
                                ->value('bidder');

            if ($currentMaxBidder == $bidderId) {
                return redirect()->back()->withErrors(['You already have the current highest bid!']);
            }
        }

        $existingBid = DB::table('bid')->where('auction_id', $request->auction_id)
                                        ->where('bidder', $bidderId)
                                        ->first();

        if ($existingBid) {
            // Update existing bid
            DB::table('bid')
            ->where('auction_id', $request->auction_id)
            ->where('bidder', $bidderId)
            ->update([
                'bidvalue' => $request->bid_amount,
                'biddate' => $bidDate,
            ]);
        } else {
            // Insert new bid
            DB::table('bid')->insert([
                'auction_id' => $request->auction_id,
                'bidder' => $bidderId,
                'bidvalue' => $request->bid_amount,
                'biddate' => $bidDate,
                'winner' => false
            ]);
        }

        $bidder->credit -= $bidAmount;
        $bidder->save();


        $user_ids = Bid::where('auction_id', $request->auction_id)
                    ->pluck('bidder');

        
        
        Log::info($user_ids);

        //Notify every user that has bidded on that auction
        foreach($user_ids as $user_id){
            if($user_id != $bidderId){
                Notification::create([
                    'user_id' => $user_id,
                    'auction_id' => $request->auction_id,
                    'information'=> 'A new bid has been placed on an auction you have bidded on.'
                ]);
            }
        }

        $auction = Auction::find($request->auction_id);
        //Notifiy auction owner
        Notification::create([
            'user_id' => $auction->owner_id,
            'auction_id' => $request->auction_id,
            'information'=> 'A new bid has been placed on an auction you own.'
        ]);

        $this->checkAndExtendAuctionTime($auctionId);
        
        // Redirect back or to another page with success message
        return redirect()->back()->with('success', 'Your bid was placed successfully!');
    }

    private function checkAndExtendAuctionTime($auction_id)
    {
        $auction = Auction::findOrFail($auction_id);
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($auction->datecreated)->addSeconds($auction->duration * 86400);

        if ($endDate->diffInMinutes($currentDate) <= 15) {
            // Extende a duração do leilão por 30 minutos
            $auction->duration = $auction->duration + (30 / 1440); // Adiciona 30 minutos em dias
            $auction->save();

            return true; // Indica que o tempo foi estendido
        }
        return false; // Indica que não foi necessário estender
    }
    
}
