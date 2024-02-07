<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Auction;
use App\Models\AuctionState;
use App\Models\User;
use App\Models\Bid;
use App\Models\Belongs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller{
    
    /**
     * Check if the current user is not an admin.
     *
     * @return bool
     */
    
     private function isNotAdmin(){
        if(Auth::user() == null || !Auth::user()->is_admin)
            return true;

        return false;
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View | \Illuminate\Http\Response | \Illuminate\Http\RedirectResponse
     */
    
    public function show(){
        if($this->isNotAdmin())
            return response('You do not have permissions to view this page.', 403);
        
        $auctions = Auction::with('categories')->get();
        $categories = Category::all();
        $users = User::all();
        $bids = Bid::all();

        return view('pages.admin', ['auctions' => $auctions, 'categories' => $categories, 'users' => $users, 'bids' => $bids]);
    }

    public function updateAuctionStatus(Request $request){
        if($this->isNotAdmin()) {
            return response('You do not have permissions to perform this action.', 403);
        }

        $auction = Auction::findOrFail($request->input('auction_id'));
        $state = AuctionState::where('state_name', $request->input('status'))->firstOrFail();

        $auction->state_id = $state->id;
        $auction->save();

        return back()->with('status', 'Auction status updated successfully!');
    }

    public function banUser($userId)
    {
        if($this->isNotAdmin()) {
            return response('You do not have permissions to perform this action.', 403);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if (Auth::id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot ban yourself.');
        }

        $user->is_banned = true;
        $user->save();

        return redirect()->back()->with('success', 'User has been banned successfully.');
    }

    public function unbanUser($userId)
    {
        if($this->isNotAdmin()) {
            return response('You do not have permissions to perform this action.', 403);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if (Auth::id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot unban yourself.');
        }

        $user->is_banned = false;
        $user->save();

        return redirect()->back()->with('success', 'User has been unbanned successfully.');
    }


    public function deleteUser($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if (Auth::user()->is_admin && Auth::id() != $user->id) {
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

            return redirect()->back()->with('success', 'User account deleted successfully.');           
        } else {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
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

    public function search(Request $request)
    {
        $query = $request->get('query');
        $users = User::query()
            ->where('username', 'LIKE', "%{$query}%") 
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->get(['id', 'username', 'email']);

        return response()->json($users);
    }

    public function createUser(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            'address' => 'required|string|min:3|max:250',
            'postalcode' => 'required|string|max:8|min:8',
            'phonenumber' => 'required|string|max:13|min:13|unique:users'
        ]);


        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'postalcode' => $request->postalcode,
            'phonenumber' => $request->phonenumber,
            'is_admin' => false,
            'is_deleted' => false,
            'is_banned' => false
        ]);


        return redirect()->back()->with('success', 'New user created successfully.');
    }

    public function updateAuctionCategory(Request $request)
    {
        $validatedData = $request->validate([
            'auction_id' => 'required|integer',
            'category' => 'required|string',
        ]);

        $auctionId = $validatedData['auction_id'];
        $newCategoryName = $validatedData['category'];

        $auction = Auction::find($auctionId);
        if ($auction) {
            $category = Category::where('categoryname', $newCategoryName)->first();

            if ($category) {
                $auction->categories()->sync([$category->categoryname]);

                return redirect()->back()->with('success', 'Auction category updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Category not found.');
            }
        }

        return redirect()->back()->with('error', 'Auction not found.');
    }

    public function storeCategory(Request $request)
    {
        $validatedData = $request->validate([
            'categoryname' => 'required|string', 
        ]);

        $category = new Category();
        $category->categoryname = $validatedData['categoryname'];
        $category->save();

        return redirect()->back()->with('success', 'Category created successfully.');
    }


}