import { ref } from 'vue';

const STORAGE_KEY = 'reading-mode';

// Module-scoped singleton: PublicLayout (chrome) and Blog/Show (article + toggle)
// import the same composable, so they all read/write one source of truth.
const isReadingMode = ref(false);

function readPreference() {
    if (typeof window === 'undefined') return false;
    try {
        return window.localStorage.getItem(STORAGE_KEY) === 'on';
    } catch {
        return false; // storage blocked (private mode) — fall back to off
    }
}

function persist(value) {
    if (typeof window === 'undefined') return;
    try {
        window.localStorage.setItem(STORAGE_KEY, value ? 'on' : 'off');
    } catch {
        /* storage unavailable — preference simply won't survive reloads */
    }
}

export function useReadingMode() {
    // User-intent transitions persist the preference across reloads.
    function enable()  { isReadingMode.value = true;  persist(true); }
    function disable() { isReadingMode.value = false; persist(false); }
    function toggle()  { isReadingMode.value ? disable() : enable(); }

    // Re-apply the stored preference — call on article-page mount.
    function restore() { isReadingMode.value = readPreference(); }

    // Drop the *active* state without touching the stored preference.
    // Used on page-leave so reading mode never bleeds onto the index/contact
    // pages, while a reload of an article still restores it.
    function deactivate() { isReadingMode.value = false; }

    return { isReadingMode, enable, disable, toggle, restore, deactivate };
}
