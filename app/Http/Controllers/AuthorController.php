<?php

namespace App\Http\Controllers;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::select('id', 'name', 'lastName', 'country', 'birthDate', 'created_at')
            ->withCount('books')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'lastName' => $author->lastName,
                    'fullName' => $author->name . ' ' . $author->lastName,
                    'country' => $author->country,
                    'birthDate' => $author->birthDate->format('Y-m-d'),
                    'formattedBirthDate' => $author->birthDate->format('d/m/Y'),
                    'registrationDate' => $author->created_at->format('d/m/Y'),
                    'booksCount' => $author->books_count,
                ];
            });

        return Inertia::render('Authors', [
            'authors' => $authors,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'birthDate' => 'required|date',
        ]);

        Author::create($validated);

        return back()->with('success', 'Autor creado exitosamente');
    }


    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        $authorData = [
            'id' => $author->id,
            'name' => $author->name,
            'lastName' => $author->lastName,
            'fullName' => $author->name . ' ' . $author->lastName,
            'country' => $author->country,
            'birthDate' => $author->birthDate->format('Y-m-d'),
            'formattedBirthDate' => $author->birthDate->format('d/m/Y'),
            'registrationDate' => $author->created_at->format('d/m/Y'),
            'books' => $author->books()->select('books.id as book_id', 'books.title', 'books.published_at', 'books.edition')->get(),
            'booksCount' => $author->books()->count(),
        ];

        return response()->json($authorData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'birthDate' => 'required|date',
        ]);

        $author->update($validated);

        return back()->with('success', 'Autor actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return back()->with('success', 'Autor eliminado exitosamente');
    }
}