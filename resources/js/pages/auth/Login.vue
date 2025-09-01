<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, CheckCircle } from 'lucide-vue-next';
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
    forgotPasswordForm.post(route('password.email'), {
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

// Close dialog and reset
const closeDialog = () => {
    isDialogOpen.value = false;
    setTimeout(() => {
        resetForgotPasswordForm();
    }, 300); // Wait for dialog close animation
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <Dialog v-if="canResetPassword" v-model:open="isDialogOpen">
                            <DialogTrigger as-child>
                                <button type="button" class="text-sm text-primary hover:underline" :tabindex="5">
                                    Forgot password?
                                </button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-md">
                                <DialogHeader>
                                    <DialogTitle class="text-center">
                                        <span v-if="!isEmailSent">Reset your password</span>
                                        <span v-else class="flex items-center justify-center gap-2">
                                            <CheckCircle class="h-5 w-5 text-green-600" />
                                            Check your email
                                        </span>
                                    </DialogTitle>
                                    <DialogDescription class="text-center">
                                        <span v-if="!isEmailSent">
                                            Enter your email address and we'll send you a link to reset your password.
                                        </span>
                                        <span v-else>
                                            We've sent a password reset link to your email address.
                                        </span>
                                    </DialogDescription>
                                </DialogHeader>

                                <div v-if="!isEmailSent" class="space-y-4">
                                    <form @submit.prevent="submitForgotPassword" class="space-y-4">
                                        <input type="hidden" name="_token" :value="$page.props.csrf_token">
                                        <div class="space-y-2">
                                            <Label for="forgot-email">Email address</Label>
                                            <Input
                                                id="forgot-email"
                                                v-model="forgotPasswordForm.email"
                                                type="email"
                                                required
                                                placeholder="email@example.com"
                                                :disabled="forgotPasswordForm.processing"
                                            />
                                            <InputError :message="forgotPasswordForm.errors.email" />
                                        </div>

                                        <div class="flex gap-2">
                                            <Button
                                                type="submit"
                                                class="flex-1"
                                                :disabled="forgotPasswordForm.processing"
                                            >
                                                <LoaderCircle v-if="forgotPasswordForm.processing" class="h-4 w-4 animate-spin mr-2" />
                                                Send reset link
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                @click="closeDialog"
                                                :disabled="forgotPasswordForm.processing"
                                            >
                                                Cancel
                                            </Button>
                                        </div>
                                    </form>
                                </div>

                                <div v-else class="space-y-4">
                                    <div class="text-center text-sm text-muted-foreground">
                                        If you don't see the email, check your spam folder.
                                    </div>
                                    <Button @click="closeDialog" class="w-full">
                                        Close
                                    </Button>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
