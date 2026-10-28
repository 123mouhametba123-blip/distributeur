<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ telephone: '', password: '' })
</script>

<template>
  <div class="max-w-sm mx-auto mt-24 p-6 border rounded-xl bg-white">
    <h1 class="text-xl font-bold mb-4">Connexion</h1>
    <form @submit.prevent="form.post('/login')" class="space-y-3">
      <input v-model="form.telephone" placeholder="Numéro de téléphone" class="w-full border p-2 rounded" />
      <input v-model="form.password" type="password" placeholder="Mot de passe" class="w-full border p-2 rounded" />
      <button :disabled="form.processing" class="w-full bg-blue-600 text-white p-2 rounded">Se connecter</button>
    </form>
  </div>
</template>