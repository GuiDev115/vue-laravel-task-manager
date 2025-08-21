<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Animações e estado
const isVisible = ref(false);
const currentFeature = ref(0);

const features = [
    {
        icon: '✅',
        title: 'Gestão de Tarefas',
        description: 'Crie, edite e organize suas tarefas de forma intuitiva'
    },
    {
        icon: '👥',
        title: 'Colaboração',
        description: 'Atribua tarefas para diferentes usuários da equipe'
    },
    {
        icon: '📊',
        title: 'Dashboard Completo',
        description: 'Visualize estatísticas e acompanhe o progresso'
    },
    {
        icon: '🔒',
        title: 'Segurança',
        description: 'Sistema de autenticação seguro e roles de usuário'
    }
];

onMounted(() => {
    isVisible.value = true;
    console.log('Landing page TaskManager carregada!'); // Debug
    
    // Rotação automática dos features
    setInterval(() => {
        currentFeature.value = (currentFeature.value + 1) % features.length;
    }, 3000);
});
</script>

<template>
    <Head title="TaskManager" />
    
    <!-- DEBUG: Landing page personalizada carregada -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900">
        <!-- Navigation -->
        <nav class="relative z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">TM</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">TaskManager</span>
                        <!-- DEBUG: Se você está vendo isso, a página personalizada está funcionando -->
                    </div>
                    
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            :href="route('login')"
                            class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors"
                        >
                            Login
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-3 py-2 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-600 transition-all transform hover:scale-105 shadow-lg"
                        >
                            Cadastrar
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 
                        class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6 transition-all duration-1000"
                        :class="{ 'translate-y-0 opacity-100': isVisible, 'translate-y-10 opacity-0': !isVisible }"
                    >
                        Organize suas 
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Tarefas
                        </span>
                        <br>
                        com Facilidade
                    </h1>
                    
                    <p 
                        class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto transition-all duration-1000 delay-300"
                        :class="{ 'translate-y-0 opacity-100': isVisible, 'translate-y-10 opacity-0': !isVisible }"
                    >
                        Um sistema completo de gerenciamento de tarefas construído com Laravel e Vue.js. 
                        Gerencie projetos, colabore com sua equipe e aumente a produtividade.
                    </p>
                    
                    <div 
                        class="flex flex-col sm:flex-row gap-4 justify-center transition-all duration-1000 delay-500"
                        :class="{ 'translate-y-0 opacity-100': isVisible, 'translate-y-10 opacity-0': !isVisible }"
                    >
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-105 shadow-lg"
                        >
                            Começar Gratuitamente
                        </Link>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="border-2 border-blue-600 text-blue-600 dark:text-blue-400 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-600 hover:text-white transition-all"
                        >
                            Fazer Login
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Por que escolher nosso TaskManager?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        Funcionalidades poderosas para maximizar sua produtividade
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div 
                        v-for="(feature, index) in features" 
                        :key="index"
                        class="text-center p-6 rounded-xl bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2"
                        :class="{ 'ring-2 ring-blue-500 scale-105': currentFeature === index }"
                    >
                        <div class="text-4xl mb-4">{{ feature.icon }}</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            {{ feature.title }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Demo Section -->
         <!-- Interface Intuitiva 
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Interface Intuitiva
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        Design moderno e responsivo para uma experiência excepcional
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-4xl mx-auto">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="h-8 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 rounded"></div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="h-24 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                            <div class="h-24 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                            <div class="h-24 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/2"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-2/3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        -->

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    Pronto para começar?
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    Junte-se a milhares de usuários que já organizam suas tarefas conosco
                </p>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg inline-block"
                >
                    Criar Conta Gratuita
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">TM</span>
                        </div>
                        <span class="text-xl font-bold">TaskManager</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Sistema de gerenciamento de tarefas desenvolvido com Laravel {{ laravelVersion }} e PHP {{ phpVersion }}
                    </p>
                    <div class="flex justify-center space-x-6 text-sm text-gray-400">
                        <span>© 2025 TaskManager</span>
                        <span>•</span>
                        <span>Todos os direitos reservados</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
