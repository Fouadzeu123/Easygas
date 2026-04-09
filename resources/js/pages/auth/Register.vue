<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useTranslate } from '@/composables/useTranslate';
import { login } from '@/routes';
import { store } from '@/routes/register';

const { __ } = useTranslate();

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthBase
        :title="__('Auth.Register.Welcome')"
        :description="__('Auth.Register.Description')"
    >
        <Head :title="__('Auth.Register.Title')" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name">{{ __("Auth.Register.Name") }}</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        class="app-input"
                    />
                    <InputError :message="errors.name" />
                </div>

                <!-- Phone -->
                <div class="grid gap-2">
                    <Label for="phone">{{ __("Auth.Register.Phone") }}</Label>
                    <Input
                        id="phone"
                        type="tel"
                        name="phone"
                        required
                        :tabindex="2"
                        class="app-input"
                    />
                    <InputError :message="(errors as any).phone" />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">{{ __("Auth.Register.Email") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        :tabindex="3"
                        autocomplete="email"
                        class="app-input"
                    />
                    <InputError :message="errors.email" />
                </div>
                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">{{ __("Auth.Register.Password") }}</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        :placeholder="__('Auth.Register.Password')"
                        class="app-input"
                    />
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="grid gap-2">
                    <Label for="password_confirmation">{{ __("Auth.Register.Confirm Password") }}</Label>
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        :tabindex="6"
                        autocomplete="new-password"
                        :placeholder="__('Auth.Register.Confirm Password')"
                        class="app-input"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full btn-primary"
                    :tabindex="7"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    {{ __("Auth.Register.Submit") }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ __("Auth.Register.Already Account") }}
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="8"
                    >{{ __("Auth.Register.Login Link") }}</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
