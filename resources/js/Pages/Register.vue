<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { telValide } from '@/composables/useMoney'

const form = useForm({
  prenom: '',
  nom: '',
  telephone: '',
  password: '',
  password_confirmation: '',
})

const peutValider = computed(
  () =>
    form.prenom &&
    form.nom &&
    telValide(form.telephone) &&
    form.password.length >= 6 &&
    form.password === form.password_confirmation
)

const inscrire = () =>
  form
    .transform((d) => ({ ...d, telephone: d.telephone.replace(/\s/g, '') }))
    .post('/register')
</script>

<template>
  <div class="max-w-sm mx-auto mt-16 p-6 border rounded-xl bg-white">
    <h1 class="text-xl font-bold mb-4">Créer mon compte</h1>
    <form @submit.prevent="inscrire" class="space-y-3">
      <input v-model="form.prenom" placeholder="Prénom" class="w-full border p-2 rounded" />
      <input v-model="form.nom" placeholder="Nom" class="w-full border p-2 rounded" />
      <input v-model="form.telephone" placeholder="Numéro de téléphone" class="w-full border p-2 rounded" />
      <input v-model="form.password" type="password" placeholder="Mot de passe (6 car. min)" class="w-full border p-2 rounded" />
      <input v-model="form.password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full border p-2 rounded" />

      <button :disabled="!peutValider || form.processing" class="w-full bg-green-600 text-white p-2 rounded disabled:opacity-40">
        Créer mon compte
      </button>
    </form>
    <p class="text-sm mt-4 text-center">
      Déjà un compte ?
      <Link href="/login" class="text-blue-600">Se connecter</Link>
    </p>
  </div>
</template>
