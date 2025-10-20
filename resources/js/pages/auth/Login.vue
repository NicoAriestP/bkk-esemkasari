<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, School, Users, Building2, GraduationCap } from 'lucide-vue-next';
import Button from 'primevue/button';
import Card from 'primevue/card';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import Checkbox from 'primevue/checkbox';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
    login_as: 'user',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const loginAsOptions = [
    {
        value: 'user',
        label: 'Staff',
        icon: 'pi pi-users',
        description: 'Admin & Staff Sekolah'
    },
    {
        value: 'student',
        label: 'Siswa / Alumni',
        icon: 'pi pi-graduation-cap',
        description: 'Siswa / Alumni SMK Negeri Purwosari'
    },
    {
        value: 'partner',
        label: 'Mitra DU/DI',
        icon: 'pi pi-building',
        description: 'Perusahaan Mitra'
    },
];
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 flex items-center justify-center p-4">
        <Head title="Login" />

        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%232563eb&quot; fill-opacity=&quot;0.1&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;2&quot;/></g></g></svg>')"></div>
        </div>

        <div class="w-full max-w-6xl mx-auto relative">
            <div class="grid lg:grid-cols-2 gap-8 items-center">

                <!-- Left Side - School Info & Image -->
                <div class="hidden lg:block space-y-8">
                    <!-- School Image -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 via-blue-900/40 to-transparent rounded-2xl z-10"></div>
                        <img
                            src="img/about_image.jpeg"
                            alt="SMK Negeri Purwosari"
                            class="w-full h-80 object-cover rounded-2xl shadow-2xl"
                        >
                        <!-- Overlay Content -->
                        <div class="absolute bottom-6 left-6 right-6 z-20 text-white">
                            <h2 class="text-2xl font-bold mb-2">Bursa Kerja Khusus EduSurya</h2>
                            <p class="text-blue-100 text-sm leading-relaxed">
                                Sistem Informasi Bursa Kerja Khusus (BKK) EduSurya untuk menghubungkan siswa dengan peluang karir terbaik
                            </p>
                        </div>
                    </div>

                    <!-- Feature Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <Card class="!p-4 !border-0 !shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white feature-card">
                            <template #content>
                                <div class="text-center">
                                    <School class="w-8 h-8 mx-auto mb-2 opacity-90" />
                                    <h3 class="font-semibold text-sm">6 Program Keahlian</h3>
                                    <p class="text-xs text-blue-400 mt-1">Berbagai Jurusan Unggulan</p>
                                </div>
                            </template>
                        </Card>

                        <Card class="!p-4 !border-0 !shadow-lg bg-gradient-to-br from-yellow-500 to-yellow-600 text-white feature-card">
                            <template #content>
                                <div class="text-center">
                                    <Users class="w-8 h-8 mx-auto mb-2 opacity-90" />
                                    <h3 class="font-semibold text-sm">1200+ Siswa</h3>
                                    <p class="text-xs text-yellow-400 mt-1">Siswa Aktif Berprestasi</p>
                                </div>
                            </template>
                        </Card>

                        <Card class="!p-4 !border-0 !shadow-lg bg-gradient-to-br from-green-500 to-green-600 text-white feature-card">
                            <template #content>
                                <div class="text-center">
                                    <Building2 class="w-8 h-8 mx-auto mb-2 opacity-90" />
                                    <h3 class="font-semibold text-sm">85+ Mitra</h3>
                                    <p class="text-xs text-green-400 mt-1">Perusahaan Partner</p>
                                </div>
                            </template>
                        </Card>

                        <Card class="!p-4 !border-0 !shadow-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white feature-card">
                            <template #content>
                                <div class="text-center">
                                    <GraduationCap class="w-8 h-8 mx-auto mb-2 opacity-90" />
                                    <h3 class="font-semibold text-sm">92% Sukses</h3>
                                    <p class="text-xs text-purple-400 mt-1">Tingkat Penempatan</p>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full max-w-md mx-auto lg:max-w-none login-form">
                    <Card class="!shadow-2xl !border-0 glass">
                        <template #header>
                            <div class="px-8 pt-8 pb-4">
                                <!-- Logo & Title -->
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg animate-float">
                                        <School class="w-8 h-8 text-white" />
                                    </div>
                                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
                                    <p class="text-gray-600 text-sm">Masuk ke sistem BKK EduSurya</p>
                                </div>

                                <!-- Status Message -->
                                <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-center text-sm font-medium text-green-600">{{ status }}</p>
                                </div>
                            </div>
                        </template>

                        <template #content>
                            <div class="px-8 pb-8">
                                <form @submit.prevent="submit" class="space-y-8">

                                    <!-- Login As Selection -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700">Login Sebagai</label>
                                        <Select
                                            v-model="form.login_as"
                                            :options="loginAsOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Pilih jenis akun"
                                            class="w-full"
                                            :class="{ 'p-invalid': form.errors.login_as }"
                                        >
                                            <template #value="slotProps">
                                                <div v-if="slotProps.value" class="flex items-center gap-3">
                                                    <i :class="loginAsOptions.find(opt => opt.value === slotProps.value)?.icon"
                                                       class="text-blue-500"></i>
                                                    <div>
                                                        <div class="font-medium">{{ loginAsOptions.find(opt => opt.value === slotProps.value)?.label }}</div>
                                                        <div class="text-xs text-gray-500">{{ loginAsOptions.find(opt => opt.value === slotProps.value)?.description }}</div>
                                                    </div>
                                                </div>
                                                <span v-else class="text-gray-500">Pilih jenis akun</span>
                                            </template>
                                            <template #option="slotProps">
                                                <div class="flex items-center gap-3 py-2">
                                                    <i :class="slotProps.option.icon" class="text-blue-500"></i>
                                                    <div>
                                                        <div class="font-medium">{{ slotProps.option.label }}</div>
                                                        <div class="text-xs text-gray-500">{{ slotProps.option.description }}</div>
                                                    </div>
                                                </div>
                                            </template>
                                        </Select>
                                        <InputError :message="form.errors.login_as" />
                                    </div>

                                    <!-- Email Input -->
                                    <div class="space-y-2">
                                        <FloatLabel>
                                            <InputText
                                                id="email"
                                                v-model="form.email"
                                                type="email"
                                                required
                                                autofocus
                                                autocomplete="email"
                                                class="w-full"
                                                :class="{ 'p-invalid': form.errors.email }"
                                            />
                                            <label for="email">Alamat Email</label>
                                        </FloatLabel>
                                        <InputError :message="form.errors.email" />
                                    </div>

                                    <!-- Password Input -->
                                    <div class="space-y-2">
                                        <FloatLabel>
                                            <Password
                                                id="password"
                                                v-model="form.password"
                                                required
                                                autocomplete="current-password"
                                                :feedback="false"
                                                toggleMask
                                                class="w-full"
                                                :class="{ 'p-invalid': form.errors.password }"
                                                :input-props="{ class: 'w-full' }"
                                            />
                                            <label for="password">Password</label>
                                        </FloatLabel>
                                        <InputError :message="form.errors.password" />
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <Checkbox
                                                id="remember"
                                                v-model="form.remember"
                                                binary
                                            />
                                            <label for="remember" class="text-sm text-gray-600 cursor-pointer">
                                                Ingat saya
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <Button
                                        type="submit"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        class="w-full !bg-gradient-to-r !from-blue-500 !to-blue-600 hover:!from-blue-600 hover:!to-blue-700 !border-0 !py-3 !text-white !font-medium !rounded-lg !shadow-lg hover:!shadow-xl !transition-all !duration-200"
                                        size="large"
                                    >
                                        <div class="flex items-center justify-center gap-2">
                                            <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin" />
                                            <i v-else class="pi pi-sign-in"></i>
                                            <span>{{ form.processing ? 'Sedang masuk...' : 'Masuk' }}</span>
                                        </div>
                                    </Button>
                                </form>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Custom card hover effects */
.feature-card:hover {
    transform: translateY(-4px);
    transition: all 0.3s ease;
}

/* Background pattern animation */
@keyframes pattern-move {
    0% { background-position: 0% 0%; }
    100% { background-position: 100% 100%; }
}

.pattern-bg {
    animation: pattern-move 20s linear infinite;
}

/* Glassmorphism effect */
.glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Login form animations */
.login-form {
    animation: slideInUp 0.8s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
