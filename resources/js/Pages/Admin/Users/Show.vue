<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: Object,
    taskStats: Object
});

const getRoleBadgeClass = (userRole) => {
    return userRole === 'admin' 
        ? 'bg-purple-100 text-purple-800'
        : 'bg-green-100 text-green-800';
};

const getRoleLabel = (userRole) => {
    return userRole === 'admin' ? 'Administrador' : 'Usuário';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pendente',
        'in_progress': 'Em Progresso',
        'completed': 'Concluída'
    };
    return labels[status] || status;
};
</script>

<template>
    <Head :title="`Usuário: ${user.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
                    Detalhes do Usuário
                </h2>
                <div class="flex space-x-3">
                    <Link :href="route('admin.users.edit', user.id)">
                        <PrimaryButton>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </PrimaryButton>
                    </Link>
                    <SecondaryButton :href="route('admin.users.index')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informações do Usuário -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center dark:bg-gray-600">
                                    <span class="text-2xl font-medium text-gray-700 dark:text-gray-200">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.name }}</h3>
                                <p class="text-lg text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                                <div class="mt-2">
                                    <span :class="['inline-flex px-3 py-1 text-sm font-semibold rounded-full', getRoleBadgeClass(user.role)]">
                                        {{ getRoleLabel(user.role) }}
                                    </span>
                                </div>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-500 dark:text-gray-400">Criado em:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">
                                            {{ new Date(user.created_at).toLocaleDateString('pt-BR', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-500 dark:text-gray-400">Última atualização:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">
                                            {{ new Date(user.updated_at).toLocaleDateString('pt-BR', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas das Tarefas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-500 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total de Tarefas</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ taskStats.total }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-yellow-500 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Pendentes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ taskStats.pending }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-600 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Em Progresso</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ taskStats.in_progress }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Concluídas</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ taskStats.completed }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarefas Recentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tarefas Recentes</h3>
                    </div>
                    <div class="overflow-hidden">
                        <div v-if="user.tasks && user.tasks.length > 0">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li v-for="task in user.tasks" :key="task.id" class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ task.title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">{{ task.description }}</p>
                                            <div class="flex items-center mt-2 space-x-4">
                                                <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusBadgeClass(task.status)]">
                                                    {{ getStatusLabel(task.status) }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ new Date(task.created_at).toLocaleDateString('pt-BR') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="px-6 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhuma tarefa</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este usuário ainda não possui tarefas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
