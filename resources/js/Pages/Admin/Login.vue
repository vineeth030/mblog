<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="w-full max-w-sm">

            <!-- Brand -->
            <div class="text-center mb-8">
                <span class="text-3xl font-bold tracking-tight text-gray-900">mblog</span>
                <p class="mt-1 text-sm text-gray-500">Admin panel</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

                <!-- Success flash (e.g. after logout) -->
                <div
                    v-if="flash.success"
                    class="mb-5 flex items-center gap-2 px-3 py-2.5 rounded-lg bg-green-50
                           border border-green-200 text-sm text-green-800"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ flash.success }}
                </div>

                <h1 class="text-xl font-semibold text-gray-900 mb-6">Sign in</h1>

                <form @submit.prevent="submit" novalidate>
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Username
                        </label>
                        <input
                            id="username"
                            v-model="form.username"
                            type="text"
                            autocomplete="username"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            :class="errors.username ? 'border-red-400' : 'border-gray-300'"
                        />
                        <p v-if="errors.username" class="mt-1.5 text-xs text-red-600">
                            {{ errors.username }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            :class="errors.password ? 'border-red-400' : 'border-gray-300'"
                        />
                        <p v-if="errors.password" class="mt-1.5 text-xs text-red-600">
                            {{ errors.password }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50
                               text-white text-sm font-semibold py-2.5 rounded-lg transition"
                    >
                        {{ form.processing ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

defineProps({
    errors: { type: Object, default: () => ({}) },
});

const page  = usePage();
const flash = computed(() => page.props.flash ?? {});

const form = useForm({ username: '', password: '' });

function submit() {
    form.post(route('admin.login.submit'), {
        onFinish: () => form.reset('password'),
    });
}
</script>
