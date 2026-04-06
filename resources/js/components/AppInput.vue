<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type Props = {
    label?: string;
    modelValue: string | number;
    type?: string;
    placeholder?: string;
    error?: string;
    class?: string;
    id: string;
    icon?: any;
    required?: boolean;
};

defineProps<Props>();
defineEmits(['update:modelValue']);
</script>

<template>
    <div :class="cn('grid gap-2 w-full', $props.class)">
        <Label :for="id" class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </Label>
        
        <div class="relative group">
            <div v-if="icon" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-easygas-green transition-colors">
                <component :is="icon" class="w-5 h-5" />
            </div>
            
            <input
                :id="id"
                :type="type || 'text'"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                :placeholder="placeholder"
                class="app-input w-full"
                :class="{ 'pl-12': icon, 'border-red-500 bg-red-50 focus:ring-red-200': error }"
            />
        </div>
        
        <span v-if="error" class="text-sm text-red-500 font-medium px-1">{{ error }}</span>
    </div>
</template>
