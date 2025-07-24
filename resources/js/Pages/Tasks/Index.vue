<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Props
const props = defineProps({
    users: Array,
    tasks: Object,
    filters: Object,
    currentUser: Object,
});

// Estado reativo
const tasks = ref(props.tasks?.data || []);
const loading = ref(false);
const showModal = ref(false);
const editingTask = ref(null);
const currentUser = ref(props.currentUser || {});
const pagination = ref({
    current_page: props.tasks?.current_page || 1,
    last_page: props.tasks?.last_page || 1,
    per_page: props.tasks?.per_page || 10,
    total: props.tasks?.total || 0,
});

// Filtros
const filters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || 'all',
});

// Form data
const form = ref({
    title: '',
    description: '',
    due_date: '',
    user_id: '',
});

// Computed
const isAdmin = computed(() => currentUser.value?.role === 'admin');
const filteredTasks = computed(() => {
    return tasks.value || [];
});

// Métodos
const loadTasks = async () => {
    loading.value = true;
    try {
        const params = {};
        if (filters.value.status !== 'all') {
            params.status = filters.value.status;
        }
        if (filters.value.search) {
            params.search = filters.value.search;
        }
        
        router.get('/tasks', params, {
            preserveState: true,
            onSuccess: (page) => {
                tasks.value = page.props.tasks?.data || [];
                pagination.value = {
                    current_page: page.props.tasks?.current_page || 1,
                    last_page: page.props.tasks?.last_page || 1,
                    per_page: page.props.tasks?.per_page || 10,
                    total: page.props.tasks?.total || 0,
                };
            },
            onFinish: () => {
                loading.value = false;
            }
        });
    } catch (error) {
        console.error('Erro ao carregar tarefas:', error);
        loading.value = false;
    }
};

const openModal = (task = null) => {
    editingTask.value = task;
    if (task) {
        form.value = { ...task };
    } else {
        form.value = {
            title: '',
            description: '',
            due_date: '',
            user_id: isAdmin.value ? '' : currentUser.value.id,
        };
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingTask.value = null;
    form.value = {
        title: '',
        description: '',
        due_date: '',
        user_id: '',
    };
};

const saveTask = async () => {
    try {
        if (editingTask.value) {
            await axios.put(`/api/tasks/${editingTask.value.id}`, form.value);
        } else {
            await axios.post('/api/tasks', form.value);
        }
        closeModal();
        loadTasks();
    } catch (error) {
        console.error('Erro ao salvar tarefa:', error);
    }
};

const toggleTask = async (task) => {
    try {
        await axios.patch(`/api/tasks/${task.id}/toggle`);
        loadTasks();
    } catch (error) {
        console.error('Erro ao alterar status da tarefa:', error);
    }
};

const deleteTask = async (task) => {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        try {
            await axios.delete(`/api/tasks/${task.id}`);
            loadTasks();
        } catch (error) {
            console.error('Erro ao excluir tarefa:', error);
        }
    }
};

const exportTasks = () => {
    window.open('/api/tasks/export/csv', '_blank');
};

// Lifecycle
onMounted(() => {
    // Tasks já carregadas via props do Inertia
});
</script>

<template>
    <Head title="Tarefas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Gerenciar Tarefas
                </h2>
                <div class="flex space-x-2">
                    <button
                        @click="exportTasks"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Exportar CSV
                    </button>
                    <button
                        v-if="isAdmin"
                        @click="openModal()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Nova Tarefa
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Buscar
                                </label>
                                <input
                                    v-model="filters.search"
                                    @input="loadTasks"
                                    type="text"
                                    placeholder="Buscar por título ou descrição..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status
                                </label>
                                <select
                                    v-model="filters.status"
                                    @change="loadTasks"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="all">Todas</option>
                                    <option value="pending">Pendentes</option>
                                    <option value="completed">Concluídas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Tarefas -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div v-if="loading" class="text-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">Carregando tarefas...</p>
                        </div>

                        <div v-else-if="tasks.length === 0" class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhuma tarefa encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ isAdmin ? 'Comece criando uma nova tarefa.' : 'Não há tarefas atribuídas a você.' }}
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="task in tasks"
                                :key="task.id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow dark:border-gray-600"
                                :class="{ 'opacity-60': task.completed }"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <button
                                                @click="toggleTask(task)"
                                                class="flex-shrink-0"
                                            >
                                                <svg
                                                    v-if="task.completed"
                                                    class="w-5 h-5 text-green-500"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <svg
                                                    v-else
                                                    class="w-5 h-5 text-gray-400 hover:text-green-500"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                            <h3
                                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                                :class="{ 'line-through': task.completed }"
                                            >
                                                {{ task.title }}
                                            </h3>
                                        </div>
                                        
                                        <p
                                            v-if="task.description"
                                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                                            :class="{ 'line-through': task.completed }"
                                        >
                                            {{ task.description }}
                                        </p>
                                        
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span v-if="task.due_date">
                                                📅 {{ new Date(task.due_date).toLocaleDateString('pt-BR') }}
                                            </span>
                                            <span v-if="task.user">
                                                👤 {{ task.user.name }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="task.completed 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' 
                                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'"
                                            >
                                                {{ task.completed ? 'Concluída' : 'Pendente' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button
                                            v-if="isAdmin || (task.user && task.user.id === currentUser.id)"
                                            @click="openModal(task)"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            v-if="isAdmin || (task.user && task.user.id === currentUser.id)"
                                            @click="deleteTask(task)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800">
                    <form @submit.prevent="saveTask">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Título *
                                    </label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Descrição
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    ></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Data de Vencimento
                                    </label>
                                    <input
                                        v-model="form.due_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                
                                <div v-if="isAdmin">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Atribuir a *
                                    </label>
                                    <select
                                        v-model="form.user_id"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">Selecione um usuário</option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.name }} ({{ user.email }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-700">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                {{ editingTask ? 'Atualizar' : 'Criar' }}
                            </button>
                            <button
                                type="button"
                                @click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
