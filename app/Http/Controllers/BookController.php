<?php

namespace App\Http\Controllers;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with([
            'authors' => function ($query) {
                $query->withTrashed();
            }
        ])->get();

        $authorExists = Author::withTrashed()->exists();
        // Only show non-deleted authors for creation
        $availableAuthors = Author::all();

        return Inertia::render('Books', [
            'books' => $books,
            'authorsExists' => $authorExists,
            'availableAuthors' => $availableAuthors
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'published_at' => 'required|date',
                'edition' => 'nullable|string|max:255',
                'cover_url' => 'nullable|url',
                'authors_id' => 'required|array|min:1',
                'authors_id.*' => 'exists:authors,id',
            ]);

            // Debug log to see what we're receiving
            Log::info('Book creation data:', $validated);

            // Use database transaction to ensure data integrity
            DB::beginTransaction();

            // Create book with only the book-specific fields
            $book = Book::create([
                'title' => $validated['title'],
                'published_at' => $validated['published_at'],
                'edition' => $validated['edition'],
                'cover_url' => $validated['cover_url']
            ]);

            Log::info('Book created with ID:', ['book_id' => $book->id]);

            // Attach authors to the book
            $book->authors()->attach($validated['authors_id']);

            Log::info('Authors attached:', ['authors' => $validated['authors_id']]);

            // Verify the book was created with authors
            $createdBook = Book::with('authors')->find($book->id);
            Log::info('Verification - Book with authors:', [
                'book' => $createdBook->toArray()
            ]);

            DB::commit();

            return back()->with('success', 'Libro creado exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Validation error during book creation:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating book:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al crear el libro: ' . $e->getMessage()]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return Inertia::render('BookShow', [
            'book' => $book->load('authors'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'published_at' => 'required|date',
            'edition' => 'nullable|string|max:255',
            'cover_url' => 'nullable|url',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,id',
        ]);

        // Debug log
        \Log::info('Book update data:', $validated);

        $book->update([
            'title' => $validated['title'],
            'published_at' => $validated['published_at'],
            'edition' => $validated['edition'],
            'cover_url' => $validated['cover_url']
        ]);

        if (isset($validated['authors'])) {
            $book->authors()->sync($validated['authors']);
        }

        return back()->with('success', 'Libro actualizado exitosamente');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index');
    }

}