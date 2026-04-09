<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Camera, Info, MapPin, Navigation, Trash2 } from 'lucide-vue-next';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { useTranslate } from '@/composables/useTranslate';

const { __ } = useTranslate();

const form = useForm({
    type:        'plastique',
    quantity:    null as number | null,
    photo:       null as File | null,
    latitude:    null as number | null,
    longitude:   null as number | null,
    description: '',
});

const photoPreview = ref<string | null>(null);
const photoInput   = ref<HTMLInputElement | null>(null);

const onPhotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];

    if (file) {
        form.photo = file;
        const reader = new FileReader();

        reader.onload = (ev) => {
            photoPreview.value = ev.target?.result as string;
        };

        reader.readAsDataURL(file);
    }
};

const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude  = position.coords.latitude;
                form.longitude = position.coords.longitude;
            },
            (err) => { console.warn('Géolocalisation refusée:', err.message); }
        );
    }
};

onMounted(() => { getLocation(); });

const submit = () => {
    if (typeof window !== 'undefined' && (window as any).route) {
        form.post((window as any).route('waste.store'));
    }
};
</script>

<template>
    <Head :title="__('Waste.Title')" />

    <MobileLayout :title="__('Waste.Title')">
        <div class="mb-6 animate-fade-in">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Trash2 class="text-easygas-green" /> {{ __("Waste.Title") }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __("Waste.Subtitle") }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6 pb-24">
            <!-- Type & Quantité -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.1s">
                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __("Waste.Type Label") }}</label>
                        <select v-model="form.type" class="app-input text-gray-800 dark:text-gray-100">
                            <option value="plastique">{{ __("Waste.Types.plastic") }}</option>
                            <option value="verre">{{ __("Waste.Types.glass") }}</option>
                            <option value="metal">{{ __("Waste.Types.metal") }}</option>
                            <option value="papier">{{ __("Waste.Types.paper") }}</option>
                            <option value="organique">{{ __("Waste.Types.organic") }}</option>
                            <option value="electronique">{{ __("Waste.Types.electronic") }}</option>
                            <option value="autre">{{ __("Waste.Types.other") }}</option>
                        </select>
                    </div>

                        <AppInput
                            id="quantity"
                            v-model="form.quantity as any"
                            type="number"
                            min="5"
                            :label="__('Waste.Quantity Label')"
                            placeholder="Ex: 5"
                            :error="form.errors.quantity"
                        />
                </div>
            </AppCard>

            <!-- Photo -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.2s">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <Camera class="w-5 h-5 text-easygas-green" /> {{ __("Waste.Photo Title") }}
                </h2>

                <div
                    @click="photoInput?.click()"
                    class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl aspect-video flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors overflow-hidden"
                >
                    <template v-if="!photoPreview">
                        <Camera class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" />
                        <span class="text-xs text-gray-400">{{ __("Waste.Photo Placeholder") }}</span>
                    </template>
                    <img v-else :src="photoPreview" class="w-full h-full object-cover" />
                </div>
                <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="onPhotoChange" />
                <span v-if="form.errors.photo" class="text-xs text-red-500 mt-2 block">{{ form.errors.photo }}</span>
            </AppCard>

            <!-- Localisation & Description -->
            <AppCard class="animate-slide-up" style="animation-delay: 0.3s">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <MapPin class="w-5 h-5 text-red-500" /> {{ __("Waste.Location Title") }}
                </h2>

                <div v-if="form.latitude" class="flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-xl border border-green-200 dark:border-green-800 mb-4">
                    <Navigation class="w-4 h-4 flex-shrink-0" />
                    <span>{{ __("Waste.GPS Saved", { lat: form.latitude.toFixed(4) }) }}</span>
                </div>
                <button v-else type="button" @click="getLocation" class="text-sm text-easygas-green font-semibold flex items-center gap-1.5 hover:underline mb-4">
                    <MapPin class="w-4 h-4" /> {{ __("Order.Get GPS") }}
                </button>

                <textarea
                    v-model="form.description"
                    :placeholder="__('Waste.Description Placeholder')"
                    class="app-input h-24 resize-none text-sm text-gray-800 dark:text-gray-100"
                ></textarea>
            </AppCard>

            <AppButton type="submit" :loading="form.processing" class="animate-slide-up w-full" style="animation-delay: 0.4s">
                {{ __("Waste.Submit") }}
            </AppButton>

            <div class="flex items-start gap-2 p-4 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800 animate-slide-up" style="animation-delay: 0.5s">
                <Info class="w-5 h-5 flex-shrink-0 text-green-600 dark:text-green-400 mt-0.5" />
                <p class="text-xs text-green-700 dark:text-green-300">{{ __("Waste.Points Info") }}</p>
            </div>
        </form>
    </MobileLayout>
</template>
