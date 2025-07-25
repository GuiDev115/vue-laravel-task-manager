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
    completed: false,
});

// Form errors
const formErrors = ref({
    title: '',
    description: '',
    due_date: '',
    user_id: '',
    completed: '',
});

// Estado para mensagens
const message = ref({
    type: '',
    text: '',
    show: false,
});

// Computed
const isAdmin = computed(() => currentUser.value?.role === 'admin');
const filteredTasks = computed(() => {
    return tasks.value || [];
});
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

// Métodos para validação
const validateForm = () => {
    const errors = {
        title: '',
        description: '',
        due_date: '',
        user_id: '',
        completed: '',
    };
    
    let hasErrors = false;
    
    // Validar título obrigatório
    if (!form.value.title || form.value.title.trim() === '') {
        errors.title = 'O título é obrigatório';
        hasErrors = true;
    } else if (form.value.title.length > 255) {
        errors.title = 'O título deve ter no máximo 255 caracteres';
        hasErrors = true;
    }
    
    // Validar descrição (opcional, mas se preenchida deve ter limite)
    if (form.value.description && form.value.description.length > 1000) {
        errors.description = 'A descrição deve ter no máximo 1000 caracteres';
        hasErrors = true;
    }
    
    // Validar usuário obrigatório para admins
    if (isAdmin.value && (!form.value.user_id || form.value.user_id === '')) {
        errors.user_id = 'Selecione um usuário para atribuir a tarefa';
        hasErrors = true;
    }
    
    // Validar data de vencimento
    if (form.value.due_date) {
        const selectedDate = new Date(form.value.due_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            errors.due_date = 'A data de vencimento deve ser hoje ou uma data futura';
            hasErrors = true;
        }
    }
    
    formErrors.value = errors;
    return !hasErrors;
};

const clearFormErrors = () => {
    formErrors.value = {
        title: '',
        description: '',
        due_date: '',
        user_id: '',
        completed: '',
    };
};

// Validação em tempo real
const validateField = (field) => {
    switch (field) {
        case 'title':
            if (!form.value.title || form.value.title.trim() === '') {
                formErrors.value.title = 'O título é obrigatório';
            } else if (form.value.title.length > 255) {
                formErrors.value.title = 'O título deve ter no máximo 255 caracteres';
            } else {
                formErrors.value.title = '';
            }
            break;
        case 'description':
            if (form.value.description && form.value.description.length > 1000) {
                formErrors.value.description = 'A descrição deve ter no máximo 1000 caracteres';
            } else {
                formErrors.value.description = '';
            }
            break;
        case 'due_date':
            if (form.value.due_date) {
                const selectedDate = new Date(form.value.due_date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    formErrors.value.due_date = 'A data de vencimento deve ser hoje ou uma data futura';
                } else {
                    formErrors.value.due_date = '';
                }
            } else {
                formErrors.value.due_date = '';
            }
            break;
        case 'user_id':
            if (isAdmin.value && (!form.value.user_id || form.value.user_id === '')) {
                formErrors.value.user_id = 'Selecione um usuário para atribuir a tarefa';
            } else {
                formErrors.value.user_id = '';
            }
            break;
    }
};

// Função para obter o token CSRF de forma robusta
const getCsrfToken = () => {
    return document.head.querySelector('meta[name="csrf-token"]')?.content || 
           document.querySelector('input[name="_token"]')?.value ||
           window.Laravel?.csrfToken ||
           window.axios.defaults.headers.common['X-CSRF-TOKEN'];
};

// Função para criar configuração padrão das requisições
const getRequestConfig = () => {
    const csrfToken = getCsrfToken();
    return {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        withCredentials: true
    };
};

const showMessage = (type, text) => {
    message.value = {
        type,
        text,
        show: true,
    };
    
    // Auto-hide após 5 segundos
    setTimeout(() => {
        message.value.show = false;
    }, 5000);
};

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
    clearFormErrors();
    
    if (task) {
        // Ao editar, preencher o formulário com os dados da tarefa
        form.value = {
            title: task.title || '',
            description: task.description || '',
            due_date: task.due_date || '',
            user_id: task.user_id || '',
            completed: task.completed || false,
        };
    } else {
        // Ao criar nova tarefa
        form.value = {
            title: '',
            description: '',
            due_date: '',
            user_id: isAdmin.value ? '' : currentUser.value.id,
            completed: false,
        };
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingTask.value = null;
    clearFormErrors();
    form.value = {
        title: '',
        description: '',
        due_date: '',
        user_id: '',
        completed: false,
    };
};

const saveTask = async () => {
    // Validar formulário antes de enviar
    if (!validateForm()) {
        showMessage('error', 'Por favor, corrija os erros no formulário');
        return;
    }
    
    // Verificar permissão para criação (apenas admins podem criar)
    if (!editingTask.value && !isAdmin.value) {
        showMessage('error', 'Apenas administradores podem criar novas tarefas');
        return;
    }
    
    // Debug: verificar se o token CSRF está disponível
    const debugCsrfToken = getCsrfToken();
    if (!debugCsrfToken) {
        console.error('CSRF token não encontrado');
        showMessage('error', 'Erro de segurança. Recarregue a página e tente novamente.');
        return;
    }
    
    try {
        loading.value = true;
        
        const config = getRequestConfig();
        
        if (editingTask.value) {
            // Editar tarefa existente
            await axios.put(`/api/tasks/${editingTask.value.id}`, form.value, config);
            showMessage('success', 'Tarefa atualizada com sucesso!');
        } else {
            // Criar nova tarefa
            await axios.post('/api/tasks', form.value, config);
            showMessage('success', 'Tarefa criada com sucesso!');
        }
        
        closeModal();
        loadTasks();
    } catch (error) {
        console.error('Erro ao salvar tarefa:', error);
        
        // Tratar erros de validação do backend
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    showMessage('error', 'Sessão expirada. Faça login novamente.');
                    break;
                case 403:
                    showMessage('error', 'Você não tem permissão para realizar esta ação');
                    break;
                case 419:
                    showMessage('error', 'Sessão expirada. Recarregue a página e tente novamente.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    break;
                case 422:
                    // Erros de validação
                    if (error.response.data && error.response.data.errors) {
                        const backendErrors = error.response.data.errors;
                        Object.keys(backendErrors).forEach(key => {
                            if (formErrors.value.hasOwnProperty(key)) {
                                formErrors.value[key] = backendErrors[key][0];
                            }
                        });
                        showMessage('error', 'Verifique os campos e tente novamente');
                    } else {
                        showMessage('error', 'Dados inválidos');
                    }
                    break;
                default:
                    showMessage('error', 'Erro ao salvar tarefa. Tente novamente.');
            }
        } else {
            showMessage('error', 'Erro de conexão. Verifique sua internet.');
        }
    } finally {
        loading.value = false;
    }
};

const toggleTask = async (task) => {
    const previousStatus = task.completed;
    const actionText = task.completed ? 'pendente' : 'concluída';
    
    try {
        // Obter token CSRF de forma mais robusta
        const csrfToken = getCsrfToken();
        
        await axios.patch(`/api/tasks/${task.id}/toggle`, {}, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            withCredentials: true
        });
        
        // Atualizar localmente o status da tarefa
        const index = tasks.value.findIndex(t => t.id === task.id);
        if (index !== -1) {
            tasks.value[index].completed = !tasks.value[index].completed;
        }
        
        showMessage('success', `Tarefa "${task.title}" marcada como ${actionText}!`);
        loadTasks();
    } catch (error) {
        console.error('Erro ao alterar status da tarefa:', error);
        
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    showMessage('error', 'Sessão expirada. Faça login novamente.');
                    break;
                case 403:
                    showMessage('error', 'Você não tem permissão para alterar esta tarefa');
                    break;
                case 419:
                    showMessage('error', 'Sessão expirada. Recarregue a página e tente novamente.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    break;
                default:
                    showMessage('error', 'Erro ao alterar status da tarefa');
            }
        } else {
            showMessage('error', 'Erro de conexão. Verifique sua internet.');
        }
    }
};

const deleteTask = async (task) => {
    if (confirm(`Tem certeza que deseja excluir a tarefa "${task.title}"? Esta ação não pode ser desfeita.`)) {
        try {
            loading.value = true;
            
            // Obter token CSRF de forma mais robusta
            const csrfToken = getCsrfToken();
            
            // Verificar se a requisição tem as credenciais corretas
            const response = await axios.delete(`/api/tasks/${task.id}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            
            // Remover localmente da lista
            tasks.value = tasks.value.filter(t => t.id !== task.id);
            
            showMessage('success', `Tarefa "${task.title}" excluída com sucesso!`);
            loadTasks();
        } catch (error) {
            console.error('Erro ao excluir tarefa:', error);
            
            if (error.response) {
                switch (error.response.status) {
                    case 401:
                        showMessage('error', 'Sessão expirada. Faça login novamente.');
                        // Opcional: redirecionar para login
                        // window.location.href = '/login';
                        break;
                    case 403:
                        showMessage('error', 'Você não tem permissão para excluir esta tarefa');
                        break;
                    case 404:
                        showMessage('error', 'Tarefa não encontrada');
                        break;
                    case 419:
                        showMessage('error', 'Sessão expirada. Recarregue a página e tente novamente.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        break;
                    default:
                        showMessage('error', 'Erro ao excluir tarefa');
                }
            } else {
                showMessage('error', 'Erro de conexão. Verifique sua internet.');
            }
        } finally {
            loading.value = false;
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Mensagens de Feedback -->
                <div 
                    v-if="message.show" 
                    class="rounded-md p-4 transition-all duration-300"
                    :class="{
                        'bg-green-50 border border-green-200 text-green-800 dark:bg-green-800/20 dark:border-green-600 dark:text-green-200': message.type === 'success',
                        'bg-red-50 border border-red-200 text-red-800 dark:bg-red-800/20 dark:border-red-600 dark:text-red-200': message.type === 'error'
                    }"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg v-if="message.type === 'success'" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg v-if="message.type === 'error'" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ message.text }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="message.show = false" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                    'text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50 dark:hover:bg-green-800/30': message.type === 'success',
                                    'text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50 dark:hover:bg-red-800/30': message.type === 'error'
                                }"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

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
                                {{ isAdmin ? 'Comece criando uma nova tarefa.' : 'Não há tarefas atribuídas a você. Entre em contato com um administrador para solicitar novas tarefas.' }}
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
                                        @input="validateField('title')"
                                        @blur="validateField('title')"
                                        type="text"
                                        required
                                        maxlength="255"
                                        :class="[
                                            'mt-1 block w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white',
                                            formErrors.title 
                                                ? 'border-red-300 dark:border-red-600' 
                                                : 'border-gray-300 dark:border-gray-600'
                                        ]"
                                        placeholder="Digite o título da tarefa"
                                    >
                                    <div class="flex justify-between mt-1">
                                        <p v-if="formErrors.title" class="text-sm text-red-600 dark:text-red-400">
                                            {{ formErrors.title }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 ml-auto">
                                            {{ form.title ? form.title.length : 0 }}/255
                                        </p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Descrição
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        @input="validateField('description')"
                                        @blur="validateField('description')"
                                        rows="3"
                                        maxlength="1000"
                                        :class="[
                                            'mt-1 block w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white',
                                            formErrors.description 
                                                ? 'border-red-300 dark:border-red-600' 
                                                : 'border-gray-300 dark:border-gray-600'
                                        ]"
                                        placeholder="Descreva os detalhes da tarefa (opcional)"
                                    ></textarea>
                                    <div class="flex justify-between mt-1">
                                        <p v-if="formErrors.description" class="text-sm text-red-600 dark:text-red-400">
                                            {{ formErrors.description }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 ml-auto">
                                            {{ form.description ? form.description.length : 0 }}/1000
                                        </p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Data de Vencimento
                                    </label>
                                    <input
                                        v-model="form.due_date"
                                        @change="validateField('due_date')"
                                        @blur="validateField('due_date')"
                                        type="date"
                                        :min="minDate"
                                        :class="[
                                            'mt-1 block w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white',
                                            formErrors.due_date 
                                                ? 'border-red-300 dark:border-red-600' 
                                                : 'border-gray-300 dark:border-gray-600'
                                        ]"
                                    >
                                    <p v-if="formErrors.due_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.due_date }}
                                    </p>
                                </div>
                                
                                <div v-if="isAdmin">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Atribuir a *
                                    </label>
                                    <select
                                        v-model="form.user_id"
                                        @change="validateField('user_id')"
                                        @blur="validateField('user_id')"
                                        required
                                        :class="[
                                            'mt-1 block w-full rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white',
                                            formErrors.user_id 
                                                ? 'border-red-300 dark:border-red-600' 
                                                : 'border-gray-300 dark:border-gray-600'
                                        ]"
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
                                    <p v-if="formErrors.user_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.user_id }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status da Tarefa
                                    </label>
                                    <div class="mt-2 space-y-2">
                                        <label class="inline-flex items-center">
                                            <input
                                                type="radio"
                                                v-model="form.completed"
                                                :value="false"
                                                class="text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                📝 Pendente
                                            </span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input
                                                type="radio"
                                                v-model="form.completed"
                                                :value="true"
                                                class="text-green-600 border-gray-300 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                ✅ Concluída
                                            </span>
                                        </label>
                                    </div>
                                    <p v-if="formErrors.completed" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.completed }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-700">
                            <button
                                type="submit"
                                :disabled="loading"
                                :class="[
                                    'w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm',
                                    loading 
                                        ? 'bg-blue-400 cursor-not-allowed' 
                                        : 'bg-blue-600 hover:bg-blue-700'
                                ]"
                            >
                                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ loading ? 'Salvando...' : (editingTask ? 'Atualizar' : 'Criar') }}
                            </button>
                            <button
                                type="button"
                                @click="closeModal"
                                :disabled="loading"
                                :class="[
                                    'mt-3 w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm',
                                    loading 
                                        ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-500 dark:text-gray-300 dark:border-gray-400'
                                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700'
                                ]"
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
