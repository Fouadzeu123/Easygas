<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useTranslate } from '@/composables/useTranslate';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

const { __ } = useTranslate();

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        :title="__('Auth.Login.Welcome')"
        :description="__('Auth.Login.Description')"
    >
        <Head :title="__('Auth.Login.Title')" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">{{ __("Auth.Login.Email") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        class="app-input"
                    />
                    <InputError :message="errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">{{ __("Auth.Login.Password") }}</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm font-medium text-easygas-green"
                            :tabindex="5"
                        >
                            {{ __("Auth.Login.Forgot") }}
                        </TextLink>
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        :placeholder="__('Auth.Login.Password')"
                        class="app-input"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span class="text-sm">{{ __("Auth.Login.Remember") }}</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full btn-primary"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    {{ __("Auth.Login.Submit") }}
                </Button>
            </div>

            <div
                class="text-center text-sm text-gray-500"
                v-if="canRegister"
            >
                {{ __("Auth.Login.No Account") }}
                <TextLink :href="register()" :tabindex="5" class="text-easygas-green font-medium underline">{{ __("Auth.Login.Register Link") }}</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
