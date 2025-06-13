<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

// Mengizinkan komponen untuk tidak mewarisi atribut secara otomatis,
// sehingga kita bisa mengontrolnya secara manual dengan v-bind="$attrs".
defineOptions({
    inheritAttrs: false,
});

// Mendefinisikan props yang bisa diterima oleh komponen ini.
interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <!--
        - Menggunakan tag <img> untuk menampilkan logo.
        - `src` menunjuk ke path gambar di dalam direktori `public`.
        - `alt` memberikan teks alternatif untuk aksesibilitas dan SEO.
        - `:class="className"` akan menerapkan kelas CSS apapun yang dioper dari parent.
        - `v-bind="$attrs"` akan menerapkan atribut HTML lainnya (misalnya, style, id, width, dll.)
          yang dioper ke komponen ini.
    -->
    <img
        src="/img/logo-smk.png"
        alt="Logo BKK"
        :class="className"
        v-bind="$attrs"
    >
</template>
