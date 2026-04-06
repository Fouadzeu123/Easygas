<script setup lang="ts">
import { cn } from '@/lib/utils';

type Props = {
    class?: string;
    hoverEffect?: boolean;
    activeEffect?: boolean;
    variant?: 'glass' | 'white' | 'outlined';
};

const props = withDefaults(defineProps<Props>(), {
    hoverEffect: true,
    activeEffect: true,
    variant: 'glass',
});
</script>

<template>
    <div
        :class="cn(
            'rounded-2xl p-4 transition-all duration-300 relative overflow-hidden',
            variant === 'glass' ? 'glass-card' : '',
            variant === 'white' ? 'bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700' : '',
            variant === 'outlined' ? 'border-2 border-gray-100 dark:border-gray-700' : '',
            hoverEffect ? 'hover:scale-[1.02] hover:shadow-lg' : '',
            activeEffect ? 'active:scale-95' : '',
            props.class || ''
        )"
    >
        <slot name="header" />
        <slot />
        <slot name="footer" />
    </div>
</template>
