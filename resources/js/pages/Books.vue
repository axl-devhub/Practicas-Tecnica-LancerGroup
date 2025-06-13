<script setup lang="ts">
import BookDialogs from '@/components/dialogs/BookDialogs.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { Author } from './Authors.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Biblioteca',
        href: '/',
    },
];

export interface Book {
    id: number;
    title: string;
    authors: Author[];
    published_at: string;
    edition: string;
    cover_url: string;
}

interface Props {
    books: Book[];
    authorsExists: boolean;
    availableAuthors: Author[];
}

// const testBooks = [
//     {
//         id: 1,
//         title: 'El Señor de los Anillos',
//         authors: ['J.R.R. Tolkien'],
//         published: '1954',
//         edition: 'Primera Edición',
//         cover_url: 'httestBookstps://m.media-amazon.com/images/I/7125+5E40JL.jpg',
//     },
//     {
//         id: 2,
//         title: '1984',
//         authors: ['George Orwell'],
//         published: '1949',
//         edition: 'Segunda Edición',
//         cover_url: 'https://m.media-amazon.com/images/I/61HkdyBpKOL.jpg',
//     },
//     {
//         id: 3,
//         title: 'El Principito',
//         authors: ['Antoine de Saint-Exupéry'],
//         published: '1943',
//         edition: 'Primera Edición',
//         cover_url: 'https://contrapunto.cl/cdn/shop/files/27522.jpg?v=1725308995',
//     },
// ];

const props = defineProps<Props>();

const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDetailsDialog = ref(false);
const selectedBook = ref<Book | null>(null);

const handleAddBook = () => {
    console.log('Add book clicked');
    console.log('Props:', props);
    console.log('Available authors:', props.availableAuthors);
    showCreateDialog.value = true;
};

const handleViewDetails = (book: Book) => {
    selectedBook.value = book;
    showDetailsDialog.value = true;
};

const handleEditBook = (book: Book) => {
    selectedBook.value = book;
    showEditDialog.value = true;
};

const handleDeleteBook = (book: Book) => {
    // TODO: Open delete confirmation dialog
    if (confirm(`¿Estás seguro de que quieres eliminar el libro "${book.title}"?`)) {
        // Simulate deletion
        router.delete(`/books/${book.id}`, {
            onSuccess: () => {
                // Optionally refresh the book list
                router.reload({ only: ['books'] });
            },
        });
    }
};

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const refreshBooks = () => {
    // This function can be used to refresh the book list if needed
    router.reload({ only: ['books'] });
};
</script>

<template>
    <Head title="Biblioteca - LancerGroup" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Biblioteca</h1>
                <Button @click="handleAddBook" class="gap-2" :disabled="!authorsExists">
                    <Plus class="h-4 w-4" />
                    Agregar Libro
                </Button>
            </div>

            <!-- No authors warning -->
            <div
                v-if="!authorsExists || (availableAuthors.length === 0 && books.length === 0)"
                class="rounded-lg border border-amber-200 bg-amber-50 p-4"
            >
                <div class="flex items-center gap-2 text-amber-800">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="font-medium">No hay autores disponibles</span>
                </div>
                <p class="mt-1 text-amber-700">
                    Necesitas crear al menos un autor antes de poder agregar libros.
                    <a href="/authors" class="underline hover:no-underline">Ve a la página de autores</a> para comenzar.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4">
                <Card v-for="book in books" :key="book.id" class="overflow-hidden pt-0 transition-shadow hover:shadow-lg">
                    <CardHeader class="p-0">
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img :src="book.cover_url" :alt="book.title" class="h-full w-full object-cover" loading="lazy" />
                        </div>
                    </CardHeader>

                    <CardContent class="px-4">
                        <h3 class="mb-2 line-clamp-2 text-xl font-semibold">{{ book.title }}</h3>

                        <div class="text-md mb-2 flex gap-3 text-muted-foreground">
                            <span class="rounded-md bg-muted px-2 py-1">
                                {{ formatDate(book.published_at) }}
                            </span>
                            <span class="rounded-md border border-border px-2 py-1">
                                {{ book.edition }}
                            </span>
                        </div>
                    </CardContent>

                    <CardFooter class="flex gap-2 p-4 pt-0">
                        <Button @click="handleViewDetails(book)" variant="outline" size="sm" class="flex-1">
                            <Eye class="h-4 w-4" />
                            Más detalles
                        </Button>
                        <Button @click="handleEditBook(book)" variant="outline" size="sm" class="flex-1">
                            <Edit class="mr-1 h-4 w-4" />
                            Editar
                        </Button>
                        <Button @click="handleDeleteBook(book)" variant="destructive" size="sm">
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty state when no books -->
            <div v-if="books.length === 0 && authorsExists" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 text-muted-foreground">
                    <svg class="mx-auto mb-4 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>

                </div>
                <h3 class="mb-2 text-lg font-semibold">No hay libros disponibles</h3>
                <p class="mb-4 text-muted-foreground">Comienza agregando tu primer libro a la biblioteca</p>
                <Button @click="handleAddBook" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Agregar Primer Libro
                </Button>
            </div>
        </div>
        <BookDialogs
            v-model:show-create="showCreateDialog"
            v-model:show-edit="showEditDialog"
            v-model:show-details="showDetailsDialog"
            :selected-book="selectedBook"
            :available-authors="availableAuthors"
            @book-created="refreshBooks"
            @book-updated="refreshBooks"
        />
    </AppLayout>
</template>
