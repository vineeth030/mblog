<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">Admin Dashboard</h1>
                <form @submit.prevent="logout">
                    <button
                        type="submit"
                        class="text-sm text-red-600 hover:text-red-800 font-medium transition"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-6 py-10">
            <p class="text-gray-600">Welcome back, admin.</p>
        </main>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

function logout() {
    router.post(route('admin.logout'));
}
</script>
