<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Notification;


use Illuminate\Database\QueryException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function showProfile($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'integer',
        ]);

        if ($validator->fails()) {
            return response('This user profile page doesnt exist.', 403);
        }

        $id = (int) $id;

        $user = User::find($id);

        if ($user->is_deleted == true) {
            return view('pages.deletedUser');
        }

        $biddingAuctions = Auction::whereIn('id', Bid::distinct('auction_id')
            ->where('bidder', $id)
            ->pluck('auction_id')
        )->get();
        
        $ownedAuctions = Auction::where('owner_id', $user->id)
        ->get();

        $profileImage = 'public/profile_pictures/';

        $average_rating = $user->average_rating;

        $followingAuctions = $user->followingAuctions()->get();

        $notifications = Notification::where('user_id', $id)->get();


        return view('profile', [
            'user' => $user,
            'ownedAuctions' => $ownedAuctions,
            'profileImage' => $profileImage, 
            'biddingAuctions' => $biddingAuctions,
            'followingAuctions' => $followingAuctions,
            'notifications' => $notifications,
            'average_rating' => $average_rating
        ]);
    }

    public function createAuctionView()
    {
        return view('pages.auctionCreate');
    }

    public function createAuction(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|numeric',
            'minvalue' => 'required|numeric'
        ];

        $request->validate($rules);

        Auction::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'datecreated' => now(),
            'minvalue' => $request->input('minvalue'),
            'starting_value' => $request->input('minvalue'),
            'duration' => $request->input('duration'),
            'owner_id' => Auth::id(),
            'state_id' => 2,
        ]);

        return redirect()->route('users.showProfile', ['id' => Auth::id()])->with('success', 'Auction created successfully');
    }

    public function edit($id)
    {
        
        if (Auth::user()->id != $id && !Auth::user()->is_admin) {
            return redirect('/users/' . $id);
        }

        $user = User::find($id);
        return view('pages.profileEdit', ['user' => $user]);
    }

    public function delete()
    {
        $user = Auth::user();
        
        // Set username and email to 'anonymous'
        $uniqueAttributes = [
            'username' => 'deleted_user',
            'email' => 'deleted_user', // Change this to a default anonymous email
            'address' => 'deleted_user',
            'phonenumber' => 'deleted_user', 
            'postalcode' => 'deleted_user'
        ];

        foreach ($uniqueAttributes as $attribute => $value) {
            $user->$attribute = $value;

            while ($this->isNotUnique($user, $attribute)) {
                $user->$attribute = $this->generateUniqueValue($attribute);
            }
        }
        $user->is_deleted = true;
        $user->save();

        Auth::logout();

        return view('pages.deletedUser');
    }

    private function isNotUnique($user, $attribute)
    {
        return User::where($attribute, $user->$attribute)
            ->where('id', '<>', $user->id)
            ->exists();
    }

    private function generateUniqueValue($attribute)
    {
        return 'deleted_user' . random_int(1000, 9999);
    }

    public function submitEdit(Request $request, $id)
    {
        /*
        if (Auth::user()->id != $id) {
            return redirect('/users/' . $id);
        }*/

        $user = User::find($id);

        $validationRules = [];

        if ($request->input('username') !== $user->username) {
            $validationRules['username'] = 'string|max:250';
        }
        if ($request->input('address') !== $user->address) {
            $validationRules['address'] = 'string|min:3|max:250';
        }
        if ($request->input('postalcode') !== $user->postalcode) {
            $validationRules['postalcode'] = 'string|max:8|min:8';
        }
        if ($request->input('phonenumber') !== $user->phonenumber) {
            $validationRules['phonenumber'] = 'string|max:13|min:13|unique:users';
        }

        $request->validate($validationRules);

        try {
            $user->username = $request->input('name');
            $user->address = $request->input('address');
            $user->postalcode = $request->input('postalcode');
            $user->phonenumber = $request->input('phonenumber');

            $user->save();

            return redirect('/users/' . $id)->with('success', 'Profile updated successfully!');
        } catch (QueryException $qe) {
            $errors = new MessageBag();
            $errors->add('An error occurred', "There was a problem editing profile information. Try again!");
            Log::error($qe->getMessage());
            return redirect('/users/' . $id)->withErrors($errors);
        }
    }

    public function addCredit(Request $request, $id)
    {
        $request->validate([
            'credit' => 'required|numeric|min:1', 
        ]);

        $user = User::findOrFail($id);
        $user->credit += $request->credit;
        $user->save();

        return back()->with('success', 'Credits successfully added.');
    }


    public function uploadProfilePicture(Request $request, $id)
        {
            $user = Auth::user();

            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = 'profile_' . $user->id . '.' . $file->getClientOriginalExtension();
                
                $file->move(public_path('profile_pictures'), $fileName);
                
                $user->profile_picture = 'profile_pictures/' . $fileName;
                $user->save();
            }
            
            return redirect()->route('users.showProfile', ['id' => $id])->with('success', 'Profile picture updated successfully!');
        }

    public function rateSeller(Request $request, $sellerId)
    {
        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5' 
        ]);

        $rating = $validatedData['rating'];

        try {
            $seller = User::findOrFail($sellerId);

            $newTotalRating = ($seller->average_rating * $seller->rating_count) + $rating;
            $seller->rating_count++;
            $seller->average_rating = $newTotalRating / $seller->rating_count;

            $seller->save();

            return back()->with('success', 'Seller rated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors('An error occurred while rating the seller.');
        }
    }
}


