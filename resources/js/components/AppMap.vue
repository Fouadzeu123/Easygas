<script setup lang="ts">
import { onMounted, ref, watch, onUnmounted } from 'vue';

const props = defineProps<{
    center?: [number, number];
    zoom?: number;
    markers?: Array<{
        position: [number, number];
        label?: string;
        icon?: 'default' | 'green' | 'orange' | 'red' | 'blue';
    }>;
    itinerary?: {
        start: [number, number];
        end: [number, number];
    };
    height?: string;
}>();

const mapContainer = ref<HTMLElement | null>(null);
let map: any = null;
let mapboxMarkers: any[] = [];
const MAPBOX_TOKEN = import.meta.env.VITE_MAPBOX_ACCESS_TOKEN;

const initMap = () => {
    if (!mapContainer.value || typeof (window as any).mapboxgl === 'undefined') {
        console.error('Mapbox GL JS not loaded or container not found');
        return;
    }

    const mapboxgl = (window as any).mapboxgl;
    mapboxgl.accessToken = MAPBOX_TOKEN;

    map = new mapboxgl.Map({
        container: mapContainer.value,
        style: 'mapbox://styles/mapbox/streets-v12',
        center: props.center ? [props.center[1], props.center[0]] : [9.7679, 4.0511],
        zoom: props.zoom || 17, // Closer street level zoom
        pitch: 45, // Tilt for better perspective
        bearing: 0,
        antialias: true,
        attributionControl: false
    });

    map.on('load', () => {
        updateMarkers();
        updateItinerary();
    });
};

const updateMarkers = () => {
    if (!map || typeof (window as any).mapboxgl === 'undefined') return;
    const mapboxgl = (window as any).mapboxgl;

    // Remove existing markers
    mapboxMarkers.forEach(m => m.remove());
    mapboxMarkers = [];

    // Add new markers
    props.markers?.forEach((m) => {
        const el = document.createElement('div');
        el.className = 'custom-marker';
        el.style.backgroundColor = getMarkerColor(m.icon);
        el.style.width = '24px';
        el.style.height = '24px';
        el.style.borderRadius = '50%';
        el.style.border = '3px solid white';
        el.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';

        const marker = new mapboxgl.Marker(el)
            .setLngLat([m.position[1], m.position[0]])
            .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(`<b>${m.label || ''}</b>`))
            .addTo(map);

        mapboxMarkers.push(marker);
    });
};

const getMarkerColor = (icon?: string) => {
    switch (icon) {
        case 'green': return '#2e7d32';
        case 'orange': return '#f57c00';
        case 'red': return '#d32f2f';
        case 'blue': return '#1976d2';
        default: return '#1976d2';
    }
};

const updateItinerary = async () => {
    if (!map || !props.itinerary) {
        if (map?.getSource('route')) {
            map.removeLayer('route');
            map.removeSource('route');
        }
        return;
    }

    const { start, end } = props.itinerary;
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[1]},${start[0]};${end[1]},${end[0]}?geometries=geojson&access_token=${MAPBOX_TOKEN}`;

    console.log('Fetching itinerary from Mapbox...', url);
    try {
        const response = await fetch(url);
        const data = await response.json();
        console.log('Mapbox directions API response:', data);
        if (!data.routes || data.routes.length === 0) {
            console.error('No routes found in Mapbox response');
            return;
        }
        const route = data.routes[0].geometry;

        if (map.getSource('route')) {
            map.getSource('route').setData({
                type: 'Feature',
                properties: {},
                geometry: route
            });
        } else {
            map.addSource('route', {
                type: 'geojson',
                data: {
                    type: 'Feature',
                    properties: {},
                    geometry: route
                }
            });

            map.addLayer({
                id: 'route',
                type: 'line',
                source: 'route',
                layout: {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                paint: {
                    'line-color': '#2e7d32',
                    'line-width': 8,
                    'line-opacity': 0.8
                }
            });

            // Fit bounds only the first time or if the trip is long
            const coordinates = route.coordinates;
            const bounds = coordinates.reduce((acc: any, coord: any) => {
                return acc.extend(coord);
            }, new (window as any).mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));

            map.fitBounds(bounds, { padding: 80, maxZoom: 16 });
        }

    } catch (error) {
        console.error('Error fetching Mapbox directions:', error);
    }
};

onMounted(() => {
    setTimeout(initMap, 300);
});

onUnmounted(() => {
    if (map) map.remove();
});

watch(() => props.markers, () => {
    if (map && map.loaded()) updateMarkers();
}, { deep: true });

watch(() => props.itinerary, () => {
    if (map && map.loaded()) updateItinerary();
}, { deep: true });

watch(() => props.center, (newCenter) => {
    if (map && newCenter) {
        map.setCenter([newCenter[1], newCenter[0]]);
    }
});
</script>

<template>
    <div 
        ref="mapContainer" 
        class="rounded-3xl overflow-hidden shadow-inner border border-gray-100 dark:border-gray-800"
        :style="{ height: height || '300px' }"
    ></div>
</template>

<style>
.custom-marker {
    cursor: pointer;
}
.mapboxgl-popup-content {
    border-radius: 1rem;
    padding: 10px;
}
</style>
