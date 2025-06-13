<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Edit, Eye, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Autores',
        href: '/authors',
    },
];

const testAuthors = [
    {
        id: 1,
        name: "Gabriel",
        lastName: "García Márquez",
        country: "Colombia",
        birthDate: "1927-03-06",
    },
    {
        id: 2,
        name: "J.K.",
        lastName: "Rowling",
        country: "United Kingdom",
        birthDate: "1965-07-31",
    },
    {
        id: 3,
        name: "Haruki",
        lastName: "Murakami",
        country: "Japan",
        birthDate: "1949-01-12",
    },
];

const handleAddAuthor = () => {
    // TODO: Open add author modal
    console.log('Add author clicked');
};

const handleViewDetails = (authorId: number) => {
    // TODO: Open details modal
    console.log('View details for author:', authorId);
};

const handleEditAuthor = (authorId: number) => {
    // TODO: Open edit modal
    console.log('Edit author:', authorId);
};

const handleDeleteAuthor = (authorId: number) => {
    // TODO: Open delete confirmation dialog
    console.log('Delete author:', authorId);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES');
};
</script>

<template>
    <Head title="Autores - LancerGroup" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Autores</h1>
                <Button @click="handleAddAuthor" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Agregar Autor
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Apellido</TableHead>
                            <TableHead>País</TableHead>
                            <TableHead>Fecha de Nacimiento</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="author in testAuthors" :key="author.id">
                            <TableCell class="font-medium">{{ author.name }}</TableCell>
                            <TableCell>{{ author.lastName }}</TableCell>
                            <TableCell>{{ author.country }}</TableCell>
                            <TableCell>{{ formatDate(author.birthDate) }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex gap-2 justify-end">
                                    <Button 
                                        @click="handleViewDetails(author.id)" 
                                        variant="outline" 
                                        size="sm"
                                    >
                                        <Eye class="h-4 w-4 mr-1" />
                                        Más Detalles
                                    </Button>
                                    <Button 
                                        @click="handleEditAuthor(author.id)" 
                                        variant="outline" 
                                        size="sm"
                                    >
                                        <Edit class="h-4 w-4 mr-1" />
                                        Editar
                                    </Button>
                                    <Button 
                                        @click="handleDeleteAuthor(author.id)" 
                                        variant="destructive" 
                                        size="sm"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Empty state when no authors -->
            <div v-if="testAuthors.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="text-muted-foreground mb-4">
                    <svg class="h-12 w-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">No hay autores disponibles</h3>
                <p class="text-muted-foreground mb-4">Comienza agregando tu primer autor</p>
                <Button @click="handleAddAuthor" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Agregar Primer Autor
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
