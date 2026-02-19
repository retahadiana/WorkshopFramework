<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->orderBy('title')->get();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:books,code',
            'title' => 'required|string',
            'author' => 'required|string',
        ]);
        Book::create($data);
        return redirect('/books')->with('status', 'Data buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:books,code,' . $book->id,
            'title' => 'required|string',
            'author' => 'required|string',
        ]);

        $book->update($data);

        return redirect('/books')->with('status', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect('/books')->with('status', 'Data buku berhasil dihapus.');
    }
}
