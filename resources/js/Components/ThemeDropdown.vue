<!-- ThemeDropdown.vue -->
<template>
    <div class="hidden sm:block">
        <Dropdown width="w-28 mt-4">
            <template #trigger>
                Theme
            </template>

            <template #content>
                <button
                    class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    :class="theme === 'light' ? 'text-gray-900 font-medium' : 'text-gray-700 font-normal dark:text-gray-400'"
                    @click="lightMode">
                    Light
                </button>

                <button
                    class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    :class="theme === 'dark' ? 'dark:text-gray-100 font-medium' : 'text-gray-700 font-normal dark:text-gray-400'"
                    @click="darkMode">
                    Dark
                </button>

                <button
                    class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    :class="theme === undefined ? 'text-gray-900 dark:text-gray-100 font-medium' : 'text-gray-700 font-normal dark:text-gray-400'"
                    @click="systemMode">
                    System
                </button>
            </template>
        </Dropdown>
    </div>
</template>

<script setup>
import { useTheme } from '@/useTheme';

// Replace this with your actual dropdown component if you're using one from a UI library
import Dropdown from '@/Components/Dropdown.vue';

const { theme, darkMode, lightMode, systemMode } = useTheme();
</script>
