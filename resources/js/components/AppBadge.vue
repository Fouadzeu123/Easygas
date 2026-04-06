<script setup lang="ts">
import { cn } from '@/lib/utils';

type Props = {
    variant?: 'default' | 'success' | 'warning' | 'error' | 'info';
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});
</script>

<template>
    <span
        :class="cn(
            'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm',
            variant === 'default' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '',
            variant === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : '',
            variant === 'warning' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' : '',
            variant === 'error' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' : '',
            variant === 'info' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' : '',
            props.class
        )"
    >
        <slot />
    </span>
</template>
