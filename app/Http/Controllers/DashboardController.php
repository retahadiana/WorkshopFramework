<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $booksCount = Book::count();
        $categoriesCount = Category::count();
        $authorsCount = Book::distinct()->count('author');
        $recentBooks = Book::with('category')->orderBy('created_at','desc')->take(6)->get();

        return view('dashboard', compact('booksCount','categoriesCount','authorsCount','recentBooks'));
    }
}
