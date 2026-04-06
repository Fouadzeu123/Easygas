<script setup lang="ts">
import BottomNavigation from '@/components/BottomNavigation.vue';
import TopAppBar from '@/components/TopAppBar.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    title?: string;
    showBottomNav?: boolean;
    showTopBar?: boolean;
}

withDefaults(defineProps<Props>(), {
    showBottomNav: true,
    showTopBar:    true,
});
</script>

<template>
    <!--
        Outer wrapper: on PC shows a grey background behind the centred phone-frame card.
        On mobile it fills the full viewport.
    -->
    <div class="min-h-screen bg-gray-100 dark:bg-gray-950 md:flex md:items-start md:justify-center md:pt-8 md:pb-12">

        <!-- Phone-frame card for PC, full-screen on mobile -->
        <div class="mobile-container">
            <Head :title="title" v-if="title" />

            <!-- Top bar -->
            <TopAppBar v-if="showTopBar !== false" :title="title" />

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto relative px-5 pt-4">
                <slot />
            </main>

            <!-- Bottom navigation -->
            <BottomNavigation v-if="showBottomNav !== false" />
        </div>
    </div>
</template>
