// resources/js/composables/useTheme.js

import { ref, watchEffect } from 'vue';
import ThemeSwitcher from './ThemeSwitcher';

const theme = ref(localStorage.getItem('theme') || undefined);

function applyTheme() {
    ThemeSwitcher.setDarkClass(theme.value);
}

function darkMode() {
    theme.value = 'dark';
    localStorage.setItem('theme', 'dark');
    applyTheme();
}

function lightMode() {
    theme.value = 'light';
    localStorage.setItem('theme', 'light');
    applyTheme();
}

function systemMode() {
    theme.value = undefined;
    localStorage.removeItem('theme');
    applyTheme();
}

// Automatically apply theme on init
applyTheme();

// Reactively update class if changed
watchEffect(() => {
    applyTheme();
});

export function useTheme() {
    return {
        theme,
        darkMode,
        lightMode,
        systemMode,
    };
}
