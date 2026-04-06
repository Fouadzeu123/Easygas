<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type Props = {
    variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'destructive';
    class?: string;
    disabled?: boolean;
    loading?: boolean;
    type?: 'button' | 'submit' | 'reset';
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    type: 'button',
});
</script>

<template>
    <Button
        :type="type"
        :disabled="disabled || loading"
        :class="cn(
            'w-full h-12 rounded-xl font-semibold transition-all active:scale-95 flex items-center justify-center gap-2',
            variant === 'primary' ? 'bg-gradient-to-r from-easygas-green to-easygas-green-dark text-white hover:opacity-90 shadow-md' : '',
            variant === 'secondary' ? 'bg-easygas-green-light text-easygas-green-dark hover:bg-opacity-80' : '',
            props.class
        )"
    >
        <slot v-if="!loading" />
        <span v-else class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Chargement...
        </span>
    </Button>
</template>
