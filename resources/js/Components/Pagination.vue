<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-between">
        <div class="flex flex-1 justify-between sm:hidden">
            <Link
                v-if="links[0].url"
                :href="links[0].url"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:text-gray-200"
            >
                Anterior
            </Link>
            <Link
                v-if="links[links.length - 1].url"
                :href="links[links.length - 1].url"
                class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:text-gray-400 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:text-gray-200"
            >
                Próximo
            </Link>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando
                    <span class="font-medium">{{ links[1]?.label || 1 }}</span>
                    de
                    <span class="font-medium">{{ links[links.length - 2]?.label || 1 }}</span>
                    páginas
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <template v-for="(link, index) in links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'relative inline-flex items-center px-2 py-2 text-sm font-medium border',
                                link.active
                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:border-indigo-400 dark:text-indigo-300'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700',
                                index === 0 ? 'rounded-l-md' : '',
                                index === links.length - 1 ? 'rounded-r-md' : ''
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            :class="[
                                'relative inline-flex items-center px-2 py-2 text-sm font-medium border border-gray-300 bg-gray-100 text-gray-400 cursor-default dark:border-gray-600 dark:bg-gray-700 dark:text-gray-500',
                                index === 0 ? 'rounded-l-md' : '',
                                index === links.length - 1 ? 'rounded-r-md' : ''
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </nav>
</template>
