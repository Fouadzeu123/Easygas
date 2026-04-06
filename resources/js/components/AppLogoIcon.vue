<script setup lang="ts">
import { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    class?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img 
        src="/img/logo-easygas.png" 
        alt="EasyGas Logo" 
        :class="['object-contain', $props.class]" 
        v-bind="$attrs" 
    />
</template>
