<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login, register } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';
// Import dialog components
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Forgot password dialog state
const isDialogOpen = ref(false);
const isEmailSent = ref(false);

// Forgot password form
const forgotPasswordForm = useForm({
    email: '',
});

// Handle forgot password submission
const submitForgotPassword = () => {
    forgotPasswordForm.post(email(), {
        onSuccess: () => {
            isEmailSent.value = true;
        },
        onError: () => {
            // Keep dialog open to show errors
        },
        onFinish: () => {
            // Reset form but keep dialog open to show success message
        },
    });
};

// Reset forgot password form
const resetForgotPasswordForm = () => {
    forgotPasswordForm.reset();
    isEmailSent.value = false;
};
</script>

<template>
    <AuthBase title="Welcome back" description="Sign in to continue and pick up where you left off">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="form.post(login())" class="flex flex-col gap-6">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <Dialog v-model:open="isDialogOpen">
                            <DialogTrigger as-child>
                                <button @click="resetForgotPasswordForm" class="text-sm text-muted-foreground hover:text-foreground underline" :tabindex="5">Forgot password?</button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-[425px]">
                                <DialogHeader>
                                    <DialogTitle class="text-2xl font-bold">Reset Password</DialogTitle>
                                    <DialogDescription class="text-muted-foreground">
                                        Enter your email address and we'll send you a link to reset your password.
                                    </DialogDescription>
                                </DialogHeader>
                                
                                <div v-if="isEmailSent" class="py-6 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="mt-4 text-lg font-medium text-green-600">Password reset link sent!</div>
                                    <div class="mt-2 text-sm text-muted-foreground">
                                        Please check your email for instructions to reset your password.
                                    </div>
                                    <Button @click="isDialogOpen = false" class="mt-6 w-full">
                                        Close
                                    </Button>
                                </div>
                                
                                <form v-else @submit.prevent="submitForgotPassword" class="space-y-4">
                                    <input type="hidden" name="_token" :value="$page.props.csrf_token">
                                    <div class="grid gap-2">
                                        <Label for="forgot-email">Email address</Label>
                                        <Input 
                                            id="forgot-email"
                                            v-model="forgotPasswordForm.email" 
                                            type="email" 
                                            autocomplete="off" 
                                            autofocus 
                                            placeholder="email@example.com" 
                                        />
                                        <InputError :message="forgotPasswordForm.errors.email" />
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <Button type="button" variant="outline" @click="isDialogOpen = false" class="w-full">
                                            Cancel
                                        </Button>
                                        <Button type="submit" class="w-full" :disabled="forgotPasswordForm.processing">
                                            <LoaderCircle v-if="forgotPasswordForm.processing" class="h-4 w-4 animate-spin" />
                                            Send Reset Link
                                        </Button>
                                    </div>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model:checked="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full btn btn-glass-cream" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="register().url" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
</template>