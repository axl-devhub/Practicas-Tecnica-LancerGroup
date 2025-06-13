<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Biblioteca',
        href: '/',
    },
];

const testBooks = [
    {
        id: 1,
        title: 'El Señor de los Anillos',
        authors: ['J.R.R. Tolkien'],
        published: '1954',
        edition: 'Primera Edición',
        cover_url: 'https://m.media-amazon.com/images/I/7125+5E40JL.jpg',
    },
    {
        id: 2,
        title: '1984',
        authors: ['George Orwell'],
        published: '1949',
        edition: 'Segunda Edición',
        cover_url: 'https://m.media-amazon.com/images/I/61HkdyBpKOL.jpg',
    },
    {
        id: 3,
        title: 'El Principito',
        authors: ['Antoine de Saint-Exupéry'],
        published: '1943',
        edition: 'Primera Edición',
        cover_url: 'https://contrapunto.cl/cdn/shop/files/27522.jpg?v=1725308995',
    },
];

const handleAddBook = () => {
    // TODO: Open add book modal
    console.log('Add book clicked');
};

const handleViewDetails = (bookId: number) => {
    // TODO: Open details modal
    console.log('View details for book:', bookId);
};

const handleEditBook = (bookId: number) => {
    // TODO: Open edit modal
    console.log('Edit book:', bookId);
};

const handleDeleteBook = (bookId: number) => {
    // TODO: Open delete confirmation dialog
    console.log('Delete book:', bookId);
};
</script>

<template>
    <Head title="Biblioteca - LancerGroup" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Biblioteca</h1>
                <Button @click="handleAddBook" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Agregar Libro
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4">
                <Card v-for="book in testBooks" :key="book.id" class="overflow-hidden pt-0 transition-shadow hover:shadow-lg">
                    <CardHeader class="p-0">
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img :src="book.cover_url" :alt="book.title" class="h-full w-full object-cover" loading="lazy" />
                        </div>
                    </CardHeader>

                    <CardContent class="px-4">
                        <h3 class="mb-2 line-clamp-2 text-xl font-semibold">{{ book.title }}</h3>

                        <div class="text-md mb-2 flex gap-3 text-muted-foreground">
                            <span class="rounded-md bg-muted px-2 py-1">
                                {{ book.published }}
                            </span>
                            <span class="rounded-md border border-border px-2 py-1">
                                {{ book.edition }}
                            </span>
                        </div>
                    </CardContent>

                    <CardFooter class="flex gap-2 p-4 pt-0">
                        <Button @click="handleViewDetails(book.id)" variant="outline" size="sm" class="flex-1">
                            <Eye class="h-4 w-4" />
                            Más detalles
                        </Button>
                        <Button @click="handleEditBook(book.id)" variant="outline" size="sm" class="flex-1">
                            <Edit class="mr-1 h-4 w-4" />
                            Editar
                        </Button>
                        <Button @click="handleDeleteBook(book.id)" variant="destructive" size="sm">
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty state when no books -->
            <div v-if="testBooks.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
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
    </AppLayout>
</template>
