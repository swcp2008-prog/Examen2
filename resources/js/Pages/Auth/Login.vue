<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sistema de Gestión de Docentes - Login" />

    <div class="min-h-screen bg-gradient-to-br from-blue-600 to-purple-700 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header con logo -->
            <div class="text-center mb-8">
                <div class="text-6xl mb-4">🏫</div>
                <h1 class="text-4xl font-bold text-white">Sistema de Docentes</h1>
                <p class="text-blue-100 mt-2">Gestión de Horarios y Asistencias</p>
            </div>

            <!-- Card de login -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div v-if="status" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg font-medium text-sm text-green-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="📧 Correo Electrónico" class="text-gray-700 font-semibold" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="admin@sistema.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="🔐 Contraseña" class="text-gray-700 font-semibold" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.remember" name="remember" />
                            <span class="ms-2 text-sm text-gray-600">Recuérdame</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col space-y-4 pt-4">
                        <PrimaryButton 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center" 
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">✓ Iniciar Sesión</span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Autenticando...
                            </span>
                        </PrimaryButton>

                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-center text-sm text-blue-600 hover:text-blue-800 underline">
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>
                </form>

                <!-- Credenciales de prueba removidas -->
            </div>

            <!-- Footer info -->
            <div class="mt-8 text-center text-blue-100 text-sm">
                <p>Sistema de Gestión de Docentes v1.0</p>
                <p class="mt-1">Desarrollado para la administración integral de docentes</p>
            </div>
        </div>
    </div>
</template>

