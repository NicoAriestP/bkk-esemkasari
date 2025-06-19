<script setup lang="ts">
import InputError from '@/components/InputError.vue';
// import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
// Import komponen Select jika Anda menggunakan Shadcn/ui atau sejenisnya
// import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'; // Contoh untuk Shadcn/ui
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Select from 'primevue/select';

defineProps<{
    status?: string;
    canResetPassword: boolean; // Ini mungkin perlu disesuaikan jika reset password juga per guard
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
    login_as: 'user', // Tambahkan login_as dengan nilai default 'user' (untuk Staff)
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const loginAsOptions = [
    { value: 'user', label: 'Staff' },
    { value: 'student', label: 'Siswa' },
    { value: 'partner', label: 'Mitra DU/DI' },
];
</script>

<template>
    <AuthBase title="Log in" description="Masukkan kredensial Anda untuk login">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="login_as">Login Sebagai</Label>
                    <Select
                        id="login_as"
                        v-model="form.login_as"
                        class="placeholder:!text-muted-foreground selection:!bg-primary selection:!text-primary-foreground dark:!bg-input/30 !border-input !flex !w-full !min-w-0 !rounded-md !border !bg-transparent !text-base !shadow-xs !transition-[color,box-shadow] !outline-none disabled:!pointer-events-none disabled:!cursor-not-allowed disabled:!opacity-50 md:!text-sm"
                        :options="loginAsOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih login sebagai"
                    />
                    <InputError :message="form.errors.login_as" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="2"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="4" />
                        <span>Ingat saya</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" /> Log in
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
