// resources/js/utils/ThemeSwitcher.js

export default {
    setDarkClass(theme) {
        if (
            theme === 'dark' ||
            (!theme &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
};
