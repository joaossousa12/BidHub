<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Auction;
use App\Http\Controllers\Controller;
use App\Models\Category;


class HomeController extends Controller{
    /*public function __construct(){
        $this->middleware('guest')->except('logout');
    }*/

    public function show() {
        $auctions = Auction::all();

        $categories = Category::all();

        //\Log::debug($categories);

        return view('pages.home', ['auctions' => $auctions, 'categories' => $categories]);
    }

    public function search(Request $request){
        $query = $request->input('query');
        $categoryName = $request->input('category'); // Get the category from the request

        // Start building the query
        $auctionsQuery = Auction::query();

        // If there's a search query, add a 'like' filter on the title
        if (!empty($query)) {
            $auctionsQuery->where(function ($subquery) use ($query) {
                $subquery->where('title', 'LIKE', "%{$query}%")
                         ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }


        // If there's a category selected, join with the 'belongs' table to filter by category
        if (!empty($categoryName)) {
            $auctionsQuery->whereHas('categories', function ($query) use ($categoryName) {
                // Specify the table name before the column name to avoid ambiguity
                $query->where('belongs.categoryname', $categoryName);
            });
        }

        // Execute the query to get the results
        $auctions = $auctionsQuery->get();

        // Pass the results to the view, along with the original search and category parameters
        return view('pages.searchResults', [
            'auctions' => $auctions,
            'query' => $query,
            'categoryName' => $categoryName // Pass the selected category name to the view
        ]);
    }
}
