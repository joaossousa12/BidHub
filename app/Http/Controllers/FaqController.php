<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Faq;

class FaqController extends Controller
{
    public function index(){
        $faqs = Faq::all();
        return view('pages.faq', ['faqs' => $faqs]);
    }
}
