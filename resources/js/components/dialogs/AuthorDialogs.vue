<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
    books?: Array<{
        id: number;
        title: string;
        published_at: string;
        edition: string;
    }>;
}

interface Props {
    showCreate: boolean;
    showEdit: boolean;
    showDetails: boolean;
    selectedAuthor: Author | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:showCreate': [value: boolean];
    'update:showEdit': [value: boolean];
    'update:showDetails': [value: boolean];
    authorCreated: [];
    authorUpdated: [];
}>();

// Create form
const createForm = useForm({
    name: '',
    lastName: '',
    country: '',
    birthDate: '',
});

// Edit form
const editForm = useForm({
    name: '',
    lastName: '',
    country: '',
    birthDate: '',
});

// Details data
const authorDetails = ref<Author | null>(null);
const loadingDetails = ref(false);

// Watch for edit dialog opening
watch(
    () => props.showEdit,
    (isOpen) => {
        if (isOpen && props.selectedAuthor) {
            editForm.reset();
            editForm.name = props.selectedAuthor.name;
            editForm.lastName = props.selectedAuthor.lastName;
            editForm.country = props.selectedAuthor.country;
            editForm.birthDate = props.selectedAuthor.birthDate;
        }
    },
);

// Watch for details dialog opening
watch(
    () => props.showDetails,
    async (isOpen) => {
        if (isOpen && props.selectedAuthor) {
            loadingDetails.value = true;
            try {
                const response = await fetch(`/authors/${props.selectedAuthor.id}`);
                authorDetails.value = await response.json();
            } catch (error) {
                console.error('Error loading author details:', error);
            } finally {
                loadingDetails.value = false;
            }
        }
    },
);

const handleCreate = () => {
    createForm.post('/authors', {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            emit('update:showCreate', false);
            emit('authorCreated');
        },
        onError: (errors) => {
            console.log('Create errors:', errors);
        },
    });
};

const handleUpdate = () => {
    if (!props.selectedAuthor) return;
    editForm.put(`/authors/${props.selectedAuthor.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Author updated successfully');
            emit('update:showEdit', false);
            emit('authorUpdated');
        },
        onError: (errors) => {
            console.log('Update errors:', errors);
        },
        onBefore: () => {
            console.log('About to update author with URL:', `/authors/${props.selectedAuthor.id}`);
        },
    });
};
</script>

<!-- Rest of your template stays exactly the same -->
<template>
    <!-- Create Dialog -->
    <Dialog :open="showCreate" @update:open="$emit('update:showCreate', $event)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>Agregar Nuevo Autor</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="handleCreate" class="space-y-4">
                <div>
                    <Label for="create-name" class="my-2">Nombre</Label>
                    <Input id="create-name" v-model="createForm.name" :class="{ 'border-red-500': createForm.errors.name }" required />
                    <p v-if="createForm.errors.name" class="mt-1 text-sm text-red-500">
                        {{ createForm.errors.name }}
                    </p>
                </div>

                <div>
                    <Label for="create-lastName" class="my-2">Apellido</Label>
                    <Input id="create-lastName" v-model="createForm.lastName" :class="{ 'border-red-500': createForm.errors.lastName }" required />
                    <p v-if="createForm.errors.lastName" class="mt-1 text-sm text-red-500">
                        {{ createForm.errors.lastName }}
                    </p>
                </div>

                <div>
                    <Label for="create-country" class="my-2">País</Label>
                    <Input id="create-country" v-model="createForm.country" :class="{ 'border-red-500': createForm.errors.country }" required />
                    <p v-if="createForm.errors.country" class="mt-1 text-sm text-red-500">
                        {{ createForm.errors.country }}
                    </p>
                </div>

                <div>
                    <Label for="create-birthDate" class="my-2">Fecha de Nacimiento</Label>
                    <Input
                        id="create-birthDate"
                        type="date"
                        v-model="createForm.birthDate"
                        :class="{ 'border-red-500': createForm.errors.birthDate }"
                        required
                    />
                    <p v-if="createForm.errors.birthDate" class="mt-1 text-sm text-red-500">
                        {{ createForm.errors.birthDate }}
                    </p>
                </div>

                <div class="flex gap-2 pt-4">
                    <Button type="submit" class="flex-1" :disabled="createForm.processing">
                        {{ createForm.processing ? 'Guardando...' : 'Agregar Autor' }}
                    </Button>
                    <Button type="button" @click="$emit('update:showCreate', false)" variant="outline" class="flex-1"> Cancelar </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Edit Dialog -->
    <Dialog :open="showEdit" @update:open="$emit('update:showEdit', $event)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>Editar Autor</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="handleUpdate" class="space-y-4">
                <input type="hidden" name="_method" value="PUT" />
                <div>
                    <Label for="edit-name" class="my-2">Nombre</Label>
                    <Input id="edit-name" v-model="editForm.name" :class="{ 'border-red-500': editForm.errors.name }" required />
                    <p v-if="editForm.errors.name" class="mt-1 text-sm text-red-500">
                        {{ editForm.errors.name }}
                    </p>
                </div>

                <div>
                    <Label for="edit-lastName" class="my-2">Apellido</Label>
                    <Input id="edit-lastName" v-model="editForm.lastName" :class="{ 'border-red-500': editForm.errors.lastName }" required />
                    <p v-if="editForm.errors.lastName" class="mt-1 text-sm text-red-500">
                        {{ editForm.errors.lastName }}
                    </p>
                </div>

                <div>
                    <Label for="edit-country" class="my-2">País</Label>
                    <Input id="edit-country" v-model="editForm.country" :class="{ 'border-red-500': editForm.errors.country }" required />
                    <p v-if="editForm.errors.country" class="mt-1 text-sm text-red-500">
                        {{ editForm.errors.country }}
                    </p>
                </div>

                <div>
                    <Label for="edit-birthDate" class="my-2">Fecha de Nacimiento</Label>
                    <Input
                        id="edit-birthDate"
                        type="date"
                        v-model="editForm.birthDate"
                        :class="{ 'border-red-500': editForm.errors.birthDate }"
                        required
                    />
                    <p v-if="editForm.errors.birthDate" class="mt-1 text-sm text-red-500">
                        {{ editForm.errors.birthDate }}
                    </p>
                </div>

                <div class="flex gap-2 pt-4">
                    <Button type="submit" class="flex-1" :disabled="editForm.processing">
                        {{ editForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </Button>
                    <Button type="button" @click="$emit('update:showEdit', false)" variant="outline" class="flex-1"> Cancelar </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
    c
    <!-- Details Dialog -->
    <Dialog :open="showDetails" @update:open="$emit('update:showDetails', $event)">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>Detalles del Autor</DialogTitle>
            </DialogHeader>

            <div v-if="loadingDetails" class="flex justify-center py-8">
                <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"></div>
            </div>

            <div v-else-if="authorDetails" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Información Personal</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Nombre completo:</span> {{ authorDetails.fullName }}</p>
                            <p><span class="font-medium">País:</span> {{ authorDetails.country }}</p>
                            <p><span class="font-medium">Fecha de nacimiento:</span> {{ authorDetails.formattedBirthDate }}</p>
                            <p><span class="font-medium">Fecha de registro:</span> {{ authorDetails.registrationDate }}</p>
                            <p><span class="font-medium">Cantidad de libros:</span> {{ authorDetails.booksCount }}</p>
                        </div>
                    </div>

                    <div v-if="authorDetails.books && authorDetails.books.length > 0">
                        <h3 class="mb-4 text-lg font-semibold">Libros Publicados</h3>
                        <div class="max-h-48 space-y-2 overflow-y-auto">
                            <div v-for="book in authorDetails.books" :key="book.id" class="rounded-lg border bg-muted/50 p-3">
                                <p class="font-medium">{{ book.title }}</p>
                                <p class="text-sm text-muted-foreground">{{ book.edition }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else>
                        <h3 class="mb-4 text-lg font-semibold">Libros Publicados</h3>
                        <p class="text-muted-foreground">Este autor no tiene libros registrados aún.</p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
