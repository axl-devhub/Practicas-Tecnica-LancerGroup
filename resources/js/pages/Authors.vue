<script setup lang="ts">
import AuthorDialogs from '@/components/dialogs/AuthorDialogs.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Edit, Eye, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Author {
    id: number;
    name: string;
    lastName: string;
    fullName: string;
    country: string;
    birthDate: string;
    formattedBirthDate: string;
    registrationDate: string;
    booksCount: number;
}

interface Props {
    authors: Author[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Autores',
        href: '/authors',
    },
];

// Dialog states
const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDetailsDialog = ref(false);
const selectedAuthor = ref<Author | null>(null);

const handleAddAuthor = () => {
    showCreateDialog.value = true;
};

const handleViewDetails = (author: Author) => {
    selectedAuthor.value = author;
    showDetailsDialog.value = true;
};

const handleEditAuthor = (author: Author) => {
    selectedAuthor.value = author;
    showEditDialog.value = true;
};

const handleDeleteAuthor = (author: Author) => {
    if (confirm(`¿Estás seguro de que quieres eliminar a ${author.fullName}?`)) {
        router.delete(`/authors/${author.id}`);
    }
};

const refreshAuthors = () => {
    router.reload({ only: ['authors'] });
};
</script>

<template>
    <Head title="Autores - LancerGroup" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
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

                            <TableHead>Libros Publicados</TableHead>
                            <TableHead>Fecha de Nacimiento</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="author in authors" :key="author.id">
                            <TableCell class="font-medium">{{ author.name }}</TableCell>
                            <TableCell>{{ author.lastName }}</TableCell>
                            <TableCell>{{ author.country }}</TableCell>

                            <TableCell>{{ author.booksCount }}</TableCell>
                            <TableCell>{{ author.formattedBirthDate }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button @click="handleViewDetails(author)" variant="outline" size="sm">
                                        <Eye class="mr-1 h-4 w-4" />
                                        Más Detalles
                                    </Button>
                                    <Button @click="handleEditAuthor(author)" variant="outline" size="sm">
                                        <Edit class="mr-1 h-4 w-4" />
                                        Editar
                                    </Button>
                                    <Button @click="handleDeleteAuthor(author)" variant="destructive" size="sm">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Empty state when no authors -->
            <div v-if="authors.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 text-muted-foreground">
                    <svg class="mx-auto mb-4 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold">No hay autores disponibles</h3>
                <p class="mb-4 text-muted-foreground">Comienza agregando tu primer autor</p>
                <Button @click="handleAddAuthor" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Agregar Primer Autor
                </Button>
            </div>
        </div>

        <!-- Author Dialogs -->
        <AuthorDialogs
            v-model:show-create="showCreateDialog"
            v-model:show-edit="showEditDialog"
            v-model:show-details="showDetailsDialog"
            :selected-author="selectedAuthor"
            @author-created="refreshAuthors"
            @author-updated="refreshAuthors"
        />
    </AppLayout>
</template>
