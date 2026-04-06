<script setup lang="ts">
const props = defineProps<{
    modelValue: boolean;
    title?: string;
}>();

defineEmits(['update:modelValue']);
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div @click="$emit('update:modelValue', false)" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-900 shadow-xl rounded-3xl border border-white/20">
                    <div class="flex items-center justify-between mb-4 font-bold text-xl text-gray-900 dark:text-gray-100">
                        <h3 id="modal-title">{{ title }}</h3>
                        <button @click="$emit('update:modelValue', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="mt-2">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
