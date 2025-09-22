import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

// Normalize any legacy or custom theme values to our canonical set
function normalizeAppearance(value: string | null): Appearance | null {
    if (!value) return null;
    if (value === 'light' || value === 'dark' || value === 'system') return value;
    if (value === 'theme-dark') return 'dark';
    if (value === 'theme-light') return 'light';
    return null;
}

export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') {
        return;
    }

    // Remove any previous theme-* classes to avoid conflicts
    const themeClassesToRemove = ['theme-light', 'theme-dark', 'theme-emerald', 'theme-rose', 'theme-purple'];
    document.documentElement.classList.remove(...themeClassesToRemove);

    if (value === 'system') {
        const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
        const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
        document.documentElement.classList.add(systemTheme === 'dark' ? 'theme-dark' : 'theme-light');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
        document.documentElement.classList.add(value === 'dark' ? 'theme-dark' : 'theme-light');
    }
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    // Prefer localStorage, but fall back to cookie set by the server/layout
    let raw = localStorage.getItem('appearance');
    if (!raw) {
        const cookieMatch = document.cookie
            .split('; ')
            .find((row) => row.startsWith('appearance='));
        if (cookieMatch) {
            raw = decodeURIComponent(cookieMatch.split('=')[1] || '');
        }
    }
    const normalized = normalizeAppearance(raw);

    // If we detect an old value (e.g., theme-dark), migrate it to canonical value
    if (normalized && raw !== normalized) {
        localStorage.setItem('appearance', normalized);
        setCookie('appearance', normalized);
    }

    return normalized as Appearance | null;
};

const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();

    updateTheme(currentAppearance || 'system');
};

export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Initialize theme from saved preference if present, otherwise keep server-provided theme
    const savedAppearance = getStoredAppearance();
    if (savedAppearance) {
        updateTheme(savedAppearance);
    }

    // Set up system theme change listener...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');

export function useAppearance() {
    onMounted(() => {
        const savedAppearance = normalizeAppearance(localStorage.getItem('appearance')) as Appearance | null;

        if (savedAppearance) {
            appearance.value = savedAppearance;
        }
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem('appearance', value);

        // Store in cookie for SSR...
        setCookie('appearance', value);

        updateTheme(value);
    }

    return {
        appearance,
        updateAppearance,
    };
}
