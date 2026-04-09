<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShoppingCart, Info, Filter } from 'lucide-vue-next';
import MobileLayout from '@/layouts/MobileLayout.vue';
import { useTranslate } from '@/composables/useTranslate';

const { __ } = useTranslate();

const products = [
    { id: 1, name: __("Store.Products.Bottle 12.5kg"), brand: 'EasyGas Blue', price: '6500 FCFA', image: 'https://img.icons8.com/color/96/gas-industry.png', color: 'bg-blue-50' },
    { id: 2, name: __("Store.Products.Bottle 6kg"), brand: 'EasyGas Green', price: '3500 FCFA', image: 'https://img.icons8.com/color/96/gas-industry.png', color: 'bg-green-50' },
    { id: 3, name: __("Store.Products.Regulator"), brand: 'EcoSafe', price: '2500 FCFA', image: 'https://img.icons8.com/color/96/pressure.png', color: 'bg-orange-50' },
    { id: 4, name: __("Store.Products.Hose"), brand: 'EcoSafe', price: '1500 FCFA', image: 'https://img.icons8.com/color/96/hose.png', color: 'bg-gray-50' },
];
</script>

<template>
    <Head :title="__('Store.Title')" />

    <MobileLayout :title="__('Store.Title')">
        <div class="px-1 pt-4 pb-24">
            <!-- Promo Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-400 p-6 rounded-3xl text-white mb-8 relative overflow-hidden shadow-lg">
                <div class="absolute -bottom-4 -right-4 opacity-20">
                    <ShoppingCart :size="100" />
                </div>
                <h2 class="text-xl font-bold mb-1 italic">{{ __("Store.Promo Title") }}</h2>
                <p class="text-xs text-blue-100 opacity-90 mb-4" v-html='__("Store.Promo Desc", { code: "ECOGAS10" })'></p>
                <button class="bg-white text-blue-600 px-4 py-2 rounded-full text-xs font-bold shadow-sm">{{ __("Store.Enjoy") }}</button>
            </div>

            <!-- Categories / Filter -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold dark:text-white italic">{{ __("Store.Popular Products") }}</h3>
                <button class="p-2 glass rounded-xl">
                    <Filter :size="18" class="text-gray-600 dark:text-gray-400" />
                </button>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div v-for="product in products" :key="product.id" class="glass-card p-0 flex flex-col group overflow-hidden border-none shadow-md bg-white dark:bg-gray-800">
                    <div :class="[product.color, 'h-32 flex items-center justify-center p-6 relative']">
                        <img :src="product.image" :alt="product.name" class="w-20 h-20 group-hover:scale-110 transition-transform duration-300 drop-shadow-lg" />
                        <button class="absolute top-2 right-2 p-1.5 glass rounded-full opacity-80">
                            <Info :size="14" class="text-gray-500" />
                        </button>
                    </div>
                    <div class="p-4 flex flex-col items-start">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">{{ product.brand }}</p>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-tight mb-2">{{ product.name }}</h4>
                        <div class="flex items-center justify-between w-full mt-auto">
                            <span class="text-easygas-green-dark dark:text-easygas-green font-bold text-xs">{{ product.price }}</span>
                            <button class="bg-easygas-accent text-white p-2 rounded-lg active:scale-90 transition-transform">
                                <ShoppingCart :size="14" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="mt-12 text-center text-gray-400">
                <p class="text-[10px]">{{ __("Store.Certification") }}</p>
            </div>
        </div>
    </MobileLayout>
</template>
