<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __invoke()
    {
        $categories = Category::where('is_visible', true) 
            ->whereHas('products', function ($query) {
                $query->where('is_available', true);
            })
            ->with(['products' => function ($query) {
                $query->where('is_available', true);
            }])
            ->get();

        return view('menu', compact('categories'));
    }
}