import { ref, onUnmounted } from 'vue';

export function useGeolocation() {
    const coords = ref<{ latitude: number | null; longitude: number | null }>({ latitude: null, longitude: null });
    const isSupported = typeof window !== 'undefined' && 'geolocation' in navigator;
    const error = ref<string | null>(null);
    const loading = ref(true);

    let watcher: number | null = null;

    const isSecure = typeof window !== 'undefined' && (window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1');

    const updateCoords = () => {
        if (!isSupported) {
            error.value = 'Géolocalisation non supportée par votre navigateur';
            loading.value = false;
            return;
        }

        if (!isSecure) {
            error.value = 'La géolocalisation nécessite une connexion HTTPS sécurisée.';
            loading.value = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                coords.value = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                loading.value = false;
                error.value = null;
            },
            (err) => {
                error.value = getErrorMessage(err);
                loading.value = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    const startWatching = () => {
        if (!isSupported || !isSecure) {
            updateCoords();
            return;
        }
        
        watcher = navigator.geolocation.watchPosition(
            (position) => {
                coords.value = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                loading.value = false;
                error.value = null;
            },
            (err) => {
                error.value = getErrorMessage(err);
                loading.value = false;
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
        );
    };

    const stopWatching = () => {
        if (watcher !== null) {
            navigator.geolocation.clearWatch(watcher);
            watcher = null;
        }
    };

    const getErrorMessage = (err: GeolocationPositionError) => {
        switch (err.code) {
            case err.PERMISSION_DENIED: return "Permission refusée. Veuillez autoriser l'accès GPS.";
            case err.POSITION_UNAVAILABLE: return "Position indisponible. Vérifiez votre signal GPS.";
            case err.TIMEOUT: return "Temps d'attente dépassé. Réessayez.";
            default: return "Erreur GPS inconnue.";
        }
    };

    onUnmounted(() => {
        stopWatching();
    });

    return {
        coords,
        isSupported,
        error,
        loading,
        updateCoords,
        startWatching,
        stopWatching,
    };
}
