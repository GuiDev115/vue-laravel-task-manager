<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object
});

const page = usePage();

// State para filtros
const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');

// State para modal de confirmação
const showConfirmDialog = ref(false);
const userToDelete = ref(null);

// Função para aplicar filtros
const applyFilters = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: role.value
    }, {
        preserveState: true,
        replace: true
    });
};

// Função para limpar filtros
const clearFilters = () => {
    search.value = '';
    role.value = '';
    router.get(route('admin.users.index'));
};

// Função para confirmar exclusão
const confirmDelete = (user) => {
    userToDelete.value = user;
    showConfirmDialog.value = true;
};

// Função para deletar usuário
const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onSuccess: () => {
                showConfirmDialog.value = false;
                userToDelete.value = null;
            }
        });
    }
};

// Badge styles baseado no role
const getRoleBadgeClass = (userRole) => {
    return userRole === 'admin' 
        ? 'bg-purple-100 text-purple-800'
        : 'bg-green-100 text-green-800';
};

const getRoleLabel = (userRole) => {
    return userRole === 'admin' ? 'Administrador' : 'Usuário';
};
</script>

<template>
    <Head title="Gerenciar Usuários" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
                    Gerenciar Usuários
                </h2>
                <Link :href="route('admin.users.create')">
                    <PrimaryButton>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Usuário
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-500 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total de Usuários</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ stats.total }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-purple-500 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Administradores</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ stats.admins }}</dd>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Usuários Regulares</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ stats.users }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">Buscar</label>
                                <TextInput
                                    v-model="search"
                                    placeholder="Nome ou email..."
                                    class="w-full"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">Tipo</label>
                                <select 
                                    v-model="role"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                                >
                                    <option value="">Todos</option>
                                    <option value="admin">Administradores</option>
                                    <option value="user">Usuários</option>
                                </select>
                            </div>
                            <div class="flex items-end space-x-2">
                                <PrimaryButton @click="applyFilters">
                                    Filtrar
                                </PrimaryButton>
                                <SecondaryButton @click="clearFilters">
                                    Limpar
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Usuários -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Usuário
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Tipo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Data de Criação
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center dark:bg-gray-600">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                        {{ user.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getRoleBadgeClass(user.role)]">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('admin.users.show', user.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Ver
                                            </Link>
                                            <Link :href="route('admin.users.edit', user.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                Editar
                                            </Link>
                                            <button 
                                                @click="confirmDelete(user)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                :disabled="user.id === $page.props.auth.user.id"
                                                :class="{ 'opacity-50 cursor-not-allowed': user.id === $page.props.auth.user.id }"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="px-6 py-4 border-t dark:border-gray-700">
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação -->
        <Modal :show="showConfirmDialog" @close="showConfirmDialog = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 dark:text-gray-100">
                    Confirmar Exclusão
                </h3>
                <p class="text-sm text-gray-600 mb-6 dark:text-gray-400">
                    Tem certeza que deseja excluir o usuário <strong>{{ userToDelete?.name }}</strong>? 
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showConfirmDialog = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteUser">
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
