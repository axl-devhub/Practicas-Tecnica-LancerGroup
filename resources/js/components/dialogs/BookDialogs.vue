<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Author } from '@/pages/Authors.vue';
import { Book } from '@/pages/Books.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface Props {
    showCreate: boolean;
    showEdit: boolean;
    showDetails: boolean;
    selectedBook: Book | null;
    availableAuthors: Author[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:showCreate': [value: boolean];
    'update:showEdit': [value: boolean];
    'update:showDetails': [value: boolean];
    bookCreated: [];
    bookUpdated: [];
}>();

// Form data using useForm - Fixed field names to match backend
const createForm = useForm({
    title: '',
    published_at: '',
    edition: '',
    cover_url: '',
    authors_id: [] as number[], // This matches the backend validation
});

const editForm = useForm({
    title: '',
    published_at: '',
    edition: '',
    cover_url: '',
    authors: [] as number[], // This matches the backend validation
});

// Placeholder image URL
const placeholderImage = 'https://via.placeholder.com/300x400/e5e7eb/9ca3af?text=No+Cover';

// Watch for edit dialog opening
watch(
    () => props.showEdit,
    (isOpen) => {
        if (isOpen && props.selectedBook) {
            editForm.reset();
            editForm.title = props.selectedBook.title;
            // Ensure published_at is formatted as YYYY-MM-DD for the date input
            if (props.selectedBook.published_at) {
                editForm.published_at = props.selectedBook.published_at.split('T')[0];
            } else {
                editForm.published_at = ''; // Handle cases where it might be null or undefined
            }
            editForm.edition = props.selectedBook.edition;
            editForm.cover_url = props.selectedBook.cover_url || '';
            editForm.authors = props.selectedBook.authors.map((author) => author.id);
            // Add a log to confirm the data after setting
            console.log('Populated editForm data:', { ...editForm.data() });
        }
    },
);

// Watch for create dialog closing to reset form
watch(
    () => props.showCreate,
    (isOpen) => {
        if (!isOpen) {
            createForm.reset();
            createForm.authors_id = [];
        }
    },
);

const handleCreate = () => {
    // Validate authors selection before submitting
    if (createForm.authors_id.length === 0) {
        // You could also set a custom error here if you want
        return;
    }

    console.log('Create form data before submit:', {
        ...createForm.data(),
        authors_id: [...createForm.authors_id],
    });

    createForm.post('/books', {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('Create success response:', page);
            createForm.reset();
            emit('update:showCreate', false);
            emit('bookCreated');
        },
        onError: (errors) => {
            console.log('Create errors:', errors);
        },
        onFinish: () => {
            console.log('Create request finished');
        },
    });
};

const handleUpdate = () => {
    if (!props.selectedBook) return;

    // Validate authors selection before submitting
    if (editForm.authors.length === 0) {
        return;
    }

    console.log('Edit form data before submit:', {
        ...editForm.data(),
        authors: [...editForm.authors],
    });

    editForm.put(`/books/${props.selectedBook.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:showEdit', false);
            emit('bookUpdated');
        },
        onError: (errors) => {
            console.log('Update errors:', errors);
        },
    });
};

const isAuthorSelected = (authorId: number, isCreate: boolean = true) => {
    if (isCreate) {
        const authorsArray = [...createForm.authors_id]; // Convert proxy to regular array
        return authorsArray.includes(authorId);
    } else {
        const authorsArray = [...editForm.authors]; // Convert proxy to regular array
        return authorsArray.includes(authorId);
    }
};

const handleAuthorToggle = (authorId: number, checked: boolean, isCreate: boolean = true) => {
    if (isCreate) {
        let currentSelectedAuthors = [...createForm.authors_id];
        const index = currentSelectedAuthors.indexOf(authorId);

        if (checked && index === -1) {
            currentSelectedAuthors.push(authorId);
        } else if (!checked && index > -1) {
            currentSelectedAuthors.splice(index, 1);
        }

        createForm.authors_id = currentSelectedAuthors;
        console.log('Create Authors updated to:', [...createForm.authors_id]);
    } else {
        let currentSelectedAuthors = [...editForm.authors];
        const index = currentSelectedAuthors.indexOf(authorId);

        if (checked && index === -1) {
            currentSelectedAuthors.push(authorId);
        } else if (!checked && index > -1) {
            currentSelectedAuthors.splice(index, 1);
        }

        editForm.authors = currentSelectedAuthors;
        console.log('Edit Authors updated to:', [...editForm.authors]);
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Add computed properties for validation
const hasSelectedAuthors = computed(() => createForm.authors_id.length > 0);
const hasSelectedAuthorsEdit = computed(() => editForm.authors.length > 0);
</script>

<template>
    <!-- Create Dialog -->
    <Dialog :open="showCreate" @update:open="$emit('update:showCreate', $event)">
        <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Agregar Nuevo Libro</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="handleCreate" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <Label for="create-title" class="text-sm font-medium">Título</Label>
                            <Input
                                id="create-title"
                                v-model="createForm.title"
                                :class="{ 'border-red-500': createForm.errors.title }"
                                placeholder="Ingrese el título del libro"
                                required
                            />
                            <p v-if="createForm.errors.title" class="mt-1 text-sm text-red-500">
                                {{ createForm.errors.title }}
                            </p>
                        </div>

                        <div>
                            <Label for="create-published" class="text-sm font-medium">Fecha de Publicación</Label>
                            <Input
                                id="create-published"
                                type="date"
                                v-model="createForm.published_at"
                                :class="{ 'border-red-500': createForm.errors.published_at }"
                                required
                            />
                            <p v-if="createForm.errors.published_at" class="mt-1 text-sm text-red-500">
                                {{ createForm.errors.published_at }}
                            </p>
                        </div>

                        <div>
                            <Label for="create-edition" class="text-sm font-medium">Edición</Label>
                            <Input
                                id="create-edition"
                                v-model="createForm.edition"
                                :class="{ 'border-red-500': createForm.errors.edition }"
                                placeholder="Primera edición, Segunda edición, etc."
                            />
                            <p v-if="createForm.errors.edition" class="mt-1 text-sm text-red-500">
                                {{ createForm.errors.edition }}
                            </p>
                        </div>

                        <div>
                            <Label for="create-cover" class="text-sm font-medium">URL de Portada (Opcional)</Label>
                            <Input
                                id="create-cover"
                                type="url"
                                v-model="createForm.cover_url"
                                :class="{ 'border-red-500': createForm.errors.cover_url }"
                                placeholder="https://ejemplo.com/portada.jpg"
                            />
                            <p v-if="createForm.errors.cover_url" class="mt-1 text-sm text-red-500">
                                {{ createForm.errors.cover_url }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column - Authors -->
                    <div>
                        <Label class="mb-3 block text-sm font-medium">Autores *</Label>
                        <div
                            class="max-h-48 space-y-2 overflow-y-auto rounded-md border p-3"
                            :class="{ 'border-red-500': !hasSelectedAuthors && createForm.authors_id.length === 0 }"
                        >
                            <div v-if="availableAuthors.length === 0" class="text-sm text-muted-foreground">No hay autores disponibles</div>
                            <div v-for="author in availableAuthors" :key="author.id" class="flex items-center space-x-2">
                                <Checkbox
                                    :id="`create-author-${author.id}`"
                                    :model-value="isAuthorSelected(author.id, true)"
                                    @update:model-value="(checked: boolean) => handleAuthorToggle(author.id, checked, true)"
                                />
                                <Label :for="`create-author-${author.id}`" class="cursor-pointer text-sm font-normal">
                                    {{ author.name }} {{ author.lastName }}
                                </Label>
                            </div>
                        </div>
                        <p v-if="createForm.errors.authors_id" class="mt-1 text-sm text-red-500">
                            {{ createForm.errors.authors_id }}
                        </p>
                        <p v-if="!hasSelectedAuthors && createForm.authors_id.length === 0" class="mt-1 text-sm text-red-500">
                            Debes seleccionar al menos un autor
                        </p>
                        <p class="mt-2 text-xs text-muted-foreground">Selecciona uno o más autores para este libro</p>
                        
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <Button type="submit" class="flex-1" :disabled="createForm.processing || !hasSelectedAuthors">
                        {{ createForm.processing ? 'Guardando...' : 'Agregar Libro' }}
                    </Button>
                    <Button type="button" @click="$emit('update:showCreate', false)" variant="outline" class="flex-1"> Cancelar </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Edit Dialog -->
    <Dialog :open="showEdit" @update:open="$emit('update:showEdit', $event)">
        <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Libro</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="handleUpdate" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <Label for="edit-title" class="text-sm font-medium">Título</Label>
                            <Input
                                id="edit-title"
                                v-model="editForm.title"
                                :class="{ 'border-red-500': editForm.errors.title }"
                                placeholder="Ingrese el título del libro"
                                required
                            />
                            <p v-if="editForm.errors.title" class="mt-1 text-sm text-red-500">
                                {{ editForm.errors.title }}
                            </p>
                        </div>

                        <div>
                            <Label for="edit-published" class="text-sm font-medium">Fecha de Publicación</Label>
                            <Input
                                id="edit-published"
                                type="date"
                                v-model="editForm.published_at"
                                :class="{ 'border-red-500': editForm.errors.published_at }"
                                required
                            />
                            <p v-if="editForm.errors.published_at" class="mt-1 text-sm text-red-500">
                                {{ editForm.errors.published_at }}
                            </p>
                        </div>

                        <div>
                            <Label for="edit-edition" class="text-sm font-medium">Edición</Label>
                            <Input
                                id="edit-edition"
                                v-model="editForm.edition"
                                :class="{ 'border-red-500': editForm.errors.edition }"
                                placeholder="Primera edición, Segunda edición, etc."
                            />
                            <p v-if="editForm.errors.edition" class="mt-1 text-sm text-red-500">
                                {{ editForm.errors.edition }}
                            </p>
                        </div>

                        <div>
                            <Label for="edit-cover" class="text-sm font-medium">URL de Portada (Opcional)</Label>
                            <Input
                                id="edit-cover"
                                type="url"
                                v-model="editForm.cover_url"
                                :class="{ 'border-red-500': editForm.errors.cover_url }"
                                placeholder="https://ejemplo.com/portada.jpg"
                            />
                            <p v-if="editForm.errors.cover_url" class="mt-1 text-sm text-red-500">
                                {{ editForm.errors.cover_url }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column - Authors -->
                    <div>
                        <Label class="mb-3 block text-sm font-medium">Autores *</Label>
                        <div
                            class="max-h-48 space-y-2 overflow-y-auto rounded-md border p-3"
                            :class="{ 'border-red-500': !hasSelectedAuthorsEdit && editForm.authors.length === 0 }"
                        >
                            <div v-if="availableAuthors.length === 0" class="text-sm text-muted-foreground">No hay autores disponibles</div>
                            <div v-for="author in availableAuthors" :key="author.id" class="flex items-center space-x-2">
                                <Checkbox
                                    :id="`edit-author-${author.id}`"
                                    :model-value="isAuthorSelected(author.id, false)"
                                    @update:model-value="(checked: boolean) => handleAuthorToggle(author.id, checked, false)"
                                />
                                <Label :for="`edit-author-${author.id}`" class="cursor-pointer text-sm font-normal">
                                    {{ author.name }} {{ author.lastName }}
                                </Label>
                            </div>
                        </div>
                        <p v-if="editForm.errors.authors" class="mt-1 text-sm text-red-500">
                            {{ editForm.errors.authors }}
                        </p>
                        <p v-if="!hasSelectedAuthorsEdit && editForm.authors.length === 0" class="mt-1 text-sm text-red-500">
                            Debes seleccionar al menos un autor
                        </p>
                        <p class="mt-2 text-xs text-muted-foreground">Selecciona uno o más autores para este libro</p>
                        
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <Button type="submit" class="flex-1" :disabled="editForm.processing || !hasSelectedAuthorsEdit">
                        {{ editForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </Button>
                    <Button type="button" @click="$emit('update:showEdit', false)" variant="outline" class="flex-1"> Cancelar </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Details Dialog -->
    <Dialog :open="showDetails" @update:open="$emit('update:showDetails', $event)">
        <DialogContent class="max-h-[90vh] max-w-4xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Detalles del Libro</DialogTitle>
            </DialogHeader>

            <div v-if="selectedBook" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Left Column - Cover Image -->
                <div class="flex justify-center">
                    <div class="relative">
                        <img
                            :src="selectedBook.cover_url || placeholderImage"
                            :alt="selectedBook.title"
                            class="h-auto w-full max-w-sm rounded-lg object-cover shadow-lg"
                            @error="$event.target.src = placeholderImage"
                        />
                        <div v-if="!selectedBook.cover_url" class="absolute inset-0 flex items-center justify-center">
                            <div class="p-4 text-center text-muted-foreground">
                                <svg class="mx-auto mb-2 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <p class="text-sm">Sin portada</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Book Details -->
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-4 text-xl font-semibold">Información del Libro</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-muted-foreground">Título:</span>
                                <p class="text-lg">{{ selectedBook.title }}</p>
                            </div>

                            <div>
                                <span class="font-medium text-muted-foreground">Fecha de Publicación:</span>
                                <p>{{ formatDate(selectedBook.published_at) }}</p>
                            </div>

                            <div>
                                <span class="font-medium text-muted-foreground">Edición:</span>
                                <p>{{ selectedBook.edition }}</p>
                            </div>

                            <div v-if="selectedBook.cover_url">
                                <span class="font-medium text-muted-foreground">URL de Portada:</span>
                                <p class="text-sm break-all text-blue-600">
                                    <a :href="selectedBook.cover_url" target="_blank" class="hover:underline">
                                        {{ selectedBook.cover_url }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-lg font-semibold">Autores</h3>
                        <div v-if="selectedBook.authors && selectedBook.authors.length > 0" class="space-y-2">
                            <div v-for="author in selectedBook.authors" :key="author.id" class="rounded-lg border bg-muted/50 p-3">
                                <p class="font-medium">{{ author.name }} {{ author.lastName }}</p>
                                <p class="text-sm text-muted-foreground">{{ author.country }}</p>
                            </div>
                        </div>
                        <div v-else class="text-muted-foreground">Este libro no tiene autores asignados.</div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
