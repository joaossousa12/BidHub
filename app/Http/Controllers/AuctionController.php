<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Belongs;
use App\Models\Notification;
use App\Models\Bid;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;



class AuctionController extends Controller
{

    /*public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }*/

    public function show($id){
    
        $auction = Auction::find($id);
    

        if (!$auction) {
            return redirect()->back()->withErrors(['Auction not found.']);
        }
        
        $average_rating = $auction->user->average_rating ?? null;

        $categoryNumber = Belongs::where('auction_id', $auction->id)->get()->first();
            if ($categoryNumber != null) {
                $categoryName = Category::where('categoryname', $categoryNumber->categoryname)->first();
                if ($categoryName != null) {
                    $categoryName = $categoryName->categoryName;
                } else {
                    $categoryName = "No category";
                }
            } else {
                $categoryName = "No category";
            }

        $query = "SELECT max(bidValue) FROM bid WHERE auction_id = ?";
        $maxBid = DB::select($query, [$id]);
        if (empty($maxBid[0]->max)) {
            $maxBid[0]->max = 0.00;
        }

        $timestamp = 'Time remaining cannot be calculated.';
        if ($auction->datecreated != null && $auction->duration != null) {
            $durationInSeconds = $auction->duration * 86400; // convert days to seconds
            $endDate = Carbon::parse($auction->datecreated)->addSeconds($durationInSeconds);
            $currentDate = Carbon::now();

            if ($endDate->isPast()) {
                $timestamp = 'Auction has ended!';
            } else {
                $timestamp = $endDate->diffForHumans($currentDate, true, false, 2); 
            }
        }

        $biddingHistory = DB::table('bid')
            ->join('users', 'users.id', '=', 'bid.bidder')
            ->where('bid.auction_id', $id)
            ->select('users.username as bidder_name', 'bid.bidvalue', 'bid.biddate')
            ->orderBy('bid.biddate', 'desc')
            ->get();


        if (Auth::check()) {
            $follows = DB::select("SELECT * FROM follows WHERE buyer_id = ? and auction_id = ?", [Auth::user()->id, $auction->id]);
            if (sizeof($follows) > 0) {
                $auction->isFollowed = true;
            } else {
                $auction->isFollowed = false;
            }
        } else {
            $auction->isFollowed = false;
        }

        if(!Auth::user()){ // user is not logged in
            if($auction->state->state_name != 'approved')
                return redirect()->back()->withErrors(['Auction either not approved yet or denied!']);
            else {
                return view('pages.auction', [
                    'auction' => $auction,
                    'categoryName' => $categoryName,
                    'maxBid' => $maxBid[0]->max,
                    'starting_value' => $auction->starting_value,
                    'timestamp' => $timestamp,
                    'biddingHistory' => $biddingHistory,
                    'average_rating' => $average_rating
                ]);
            }
        }
        else if(!Auth::user()->is_admin && $auction->state->state_name != 'approved'){ // user is logged in but isn't an admin
            return redirect()->back()->withErrors(['Auction either not approved yet or denied!']);
        } else{
            return view('pages.auction', [
                'auction' => $auction,
                'categoryName' => $categoryName,
                'maxBid' => $maxBid[0]->max,
                'starting_value' => $auction->starting_value,
                'timestamp' => $timestamp,
                'biddingHistory' => $biddingHistory,
                'average_rating' => $average_rating
            ]);
        }
    }

    public function edit($id)
    {
        $auction = Auction::find($id);

        if (!Auth::check() || ($auction->owner_id != Auth::user()->id && !Auth::user()->is_admin)) {
            return redirect('/auction/' . $id)->withErrors(['You do not have permission to edit this auction.']);
        }

        return view('pages.auctionEdit', ['desc' => $auction->description, 'id' => $id]);
    }


    /*
    public function submitEdit(Request $request, $id)
    {
        $auction = Auction::find($id);
        
        if ($auction->owner_id != Auth::user()->id) {
            return redirect('/auction/' . $id);
        }

        try {
            DB::beginTransaction();

            $existingModification = AuctionModification::where('idapprovedauction', $id)
                ->whereNull('is_approved')
                ->count();

            if ($existingModification === 0) {
                $newDescription = $request->input('description');
                $modID = AuctionModification::insertGetId([
                    'newdescription' => $newDescription,
                    'idapprovedauction' => $id
                ]);

                DB::commit();
            } else {
                DB::rollback();
                $errors = new MessageBag();
                $errors->add('An error occurred', "There is already a request to edit this auction's information");
                return redirect('/auction/' . $id)->withErrors($errors);
            }
        } catch (QueryException $qe) {
            DB::rollback();
            $errors = new MessageBag();
            $errors->add('An error occurred', "There was a problem editing auction information. Try Again!");
            $this->warn($qe);
            return redirect('/auction/' . $id)->withErrors($errors);
    }

    return redirect('/auction/' . $id);
    }
    */


    public function submitEdit(Request $request, $id)
    {
        $auction = Auction::find($id);
        
        /*
        if ($auction->owner_id != Auth::user()->id) {
            return redirect('/auction/' . $id);
        }*/

        try {
            $newDescription = $request->input('description');

            Auction::where('id', $id)->update(['description' => $newDescription]);

            return redirect('/auction/' . $id)->with('success', 'Auction information updated successfully!');
        } catch (QueryException $qe) {
            $errors = new MessageBag();
            $errors->add('An error occurred', "There was a problem editing auction information. Try Again!");
            $this->warn($qe);
            return redirect('/auction/' . $id)->withErrors($errors);
        }
    }

    public function cancelAuction($id)
    {
        $auction = Auction::findOrFail($id);

        $bids = Bid::where('auction_id', $id)->count();

        if ($bids > 0) {
            
            return back()->withErrors(['You are not authorized to cancel this auction.']);
        } 

        if (Auth::id() == $auction->owner_id) {

            //Notify auction owner
            Notification::create([
                'user_id' => $auction->owner_id,
                'auction_id' => $auction->id,
                'information'=> 'An auction you own has been canceled'
            ]);

            $user_ids = DB::table('follows')
            ->where('auction_id', $auction->id)
            ->pluck('buyer_id');

            foreach($user_ids as $user_id){
                    Notification::create([
                        'user_id' => $user_id,
                        'auction_id' => $auction->id,
                        'information'=> 'An auction you follow has been canceled'
                    ]);
            }

            //Notify all users of have followed this auction

            $auction->state_id = 4;
            $auction->save();
    
            return redirect('/')->with('success', 'Auction cancelled successfully.');
        }

            return back()->withErrors(['You are not authorized to cancel this auction.']);
    }


    public function addToWishlist($auction_id)
    {
        $buyer_id = Auth::id(); // Get the authenticated user's ID

        // Insert the follow record only if it does not exist to avoid duplicates
        $exists = DB::table('follows')
                    ->where('buyer_id', $buyer_id)
                    ->where('auction_id', $auction_id)
                    ->exists();

        if (!$exists) {
            DB::table('follows')->insert([
                'buyer_id' => $buyer_id,
                'auction_id' => $auction_id
            ]);
            return back()->with('success', 'Added to wishlist.');
        } else {
            return back()->with('info', 'This auction is already in your wishlist.');
        }
    }

    public function removeFromWishlist($auction_id)
    {
        $buyer_id = Auth::id(); // Get the authenticated user's ID

        // Delete the follow record
        DB::table('follows')
            ->where('buyer_id', $buyer_id)
            ->where('auction_id', $auction_id)
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }

    public static function createTimestamp($dateApproved, $duration)
    {
        $start = strtotime($dateApproved);
        $end = $start + $duration;
        $current = time();
        $time = $end - $current;

        if ($time <= 0) {
            return "Auction has ended!";
        }

        $ts = "";
        $ts .= intdiv($time, 86400) . "d ";
        $time = $time % 86400;
        $ts .= intdiv($time, 3600) . "h ";
        $time = $time % 3600;
        $ts .= intdiv($time, 60) . "m ";
        $ts .= $time % 60 . "s";

        if (strpos($ts, "0d ") !== false) {
            $ts = str_replace("0d ", "", $ts);
            if (strpos($ts, "0h ") !== false) {
                $ts = str_replace("0h ", "", $ts);
                if (strpos($ts, "0m ") !== false) {
                    $ts = str_replace("0m ", "", $ts);
                    if (strpos($ts, "0s") !== false) {
                        $ts = "Auction has ended!";
                    }
                }
            }
        }
        return $ts;
    }

    public function timeEnd($id)
    {
        $auction = Auction::findOrFail($id);

        $user_ids = Bid::where('auction_id', $id)
                    ->pluck('bidder');

        Log::info($user_ids);
        Log::info("Chegou cá");


        //Notify every user that has bidded on that auction
        foreach($user_ids as $user_id){
                Notification::create([
                    'user_id' => $user_id,
                    'auction_id' => $id,
                    'information'=> 'An auction you have bidded on has reached its time limit and ended'
                ]);
        }

        //Notify the owner of this auction
        Notification::create([
            'user_id' => $auction->owner_id,
            'auction_id' => $id,
            'information'=> 'An auction you own has reached its time limit and ended'
        ]);

        return response()->json(['message' => 'Auction has ended successfully']);
    }
    
    public function timeEnding($id)
    {
        $auction = Auction::findOrFail($id);

        $user_ids = Bid::where('auction_id', $id)
                    ->pluck('bidder');
  
        Log::info($user_ids);
        Log::info("Chegou cá");


        //Notify every user that has bidded on that auction
        foreach($user_ids as $user_id){
                Notification::create([
                    'user_id' => $user_id,
                    'auction_id' => $id,
                    'information'=> 'An auction you have bidded on is nearing its ending'
                ]);
        }

        //Notify the owner of this auction
        Notification::create([
            'user_id' => $auction->owner_id,
            'auction_id' => $id,
            'information'=> 'An auction you own is nearing its ending'
        ]);

        return response()->json(['message' => 'Auction is about to end successfully']);
    }

    public function winner($id){
        
        $auction = Auction::findOrFail($id);

        $highestBid = Bid::where('auction_id', $id)->orderBy('bidvalue', 'desc')->first();
        $winner = $highestBid->bidder;

        $owner = $auction->owner_id;
        Log::info("passou da query");
        Log::info($winner);
        
        //Notify the winner of this auction
        Notification::create([
            'user_id' => $winner,
            'auction_id' => $id,
            'information'=> 'You have won an auction'
        ]);

        //Notify the owner of this auction
        Notification::create([
            'user_id' => $owner,
            'auction_id' => $id,
            'information'=> 'An auction you own has been won'
        ]);
        return response()->json(['message' => 'Auction has been won successfully']);

    }
    
}