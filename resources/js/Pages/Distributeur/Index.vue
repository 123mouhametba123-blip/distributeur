<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { formatFCFA, telValide } from '@/composables/useMoney'

const props = defineProps({ clients: Array, solde: [Number, String] })
// Inscription d'un client
const inscription = useForm({ nom: '', prenom: '', telephone: '', password: '' })
const inscriptionOk = computed(
  () =>
    inscription.nom &&
    inscription.prenom &&
    telValide(inscription.telephone) &&
    inscription.password.length >= 6
)
const inscrire = () =>
  inscription.post('/distributeur/clients', { onSuccess: () => inscription.reset() })

// Crédit mobile money
const credit = useForm({ client_id: '', montant: '' })
const creditOk = computed(() => credit.client_id && Number(credit.montant) > 0 &&
    Number(credit.montant) <= Number(props.solde))
const crediter = () =>
  credit.post('/distributeur/credit', { onSuccess: () => credit.reset('montant') })

// Recherche locale
const recherche = ref('')
const filtres = computed(() =>
  props.clients.filter((c) =>
    `${c.nom} ${c.prenom} ${c.telephone}`.toLowerCase().includes(recherche.value.toLowerCase())
  )
)
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 space-y-8">
    <div class="flex justify-between">
      <h1 class="text-2xl font-bold">Espace distributeur</h1>
      <div class="bg-blue-600 text-white rounded-xl p-6">
  <p class="text-sm opacity-80">Mon solde</p>
  <p class="text-3xl font-bold">{{ formatFCFA(solde) }}</p>
</div>
      <button @click="router.post('/logout')" class="text-red-600">Déconnexion</button>
    </div>

    <form @submit.prevent="inscrire" class="grid grid-cols-2 gap-3 border p-4 rounded-xl bg-white">
      <h2 class="col-span-2 font-semibold">Inscrire un client</h2>
      <input v-model="inscription.prenom" placeholder="Prénom" class="border p-2 rounded" />
      <input v-model="inscription.nom" placeholder="Nom" class="border p-2 rounded" />
      <input v-model="inscription.telephone" placeholder="Téléphone" class="border p-2 rounded" />
      <input v-model="inscription.password" type="password" placeholder="Mot de passe (6 car. min)" class="border p-2 rounded" />
      <button :disabled="!inscriptionOk || inscription.processing" class="col-span-2 bg-green-600 text-white p-2 rounded disabled:opacity-40">
        Inscrire
      </button>
    </form>

    <form @submit.prevent="crediter" class="flex gap-3 border p-4 rounded-xl items-end bg-white">
      <select v-model="credit.client_id" class="border p-2 rounded flex-1">
        <option value="">Choisir un client</option>–
        <option v-for="c in clients" :key="c.id" :value="c.id">
          {{ c.prenom }} {{ c.nom }} – {{ c.telephone }} – {{ c.id }}
        </option>
      </select>
      <input v-model="credit.montant" type="number" min="1" placeholder="Montant" class="border p-2 rounded w-40" />
      <button :disabled="!creditOk || credit.processing" class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-40">
        Créditer
      </button>
    </form>

    <div>
      <input v-model="recherche" placeholder="Rechercher un client…" class="border p-2 rounded w-full mb-3" />
      <table class="w-full text-left">
        <thead><tr><th>Client</th><th>Téléphone</th><th>Solde</th></tr></thead>
        <tbody>
          <tr v-for="c in filtres" :key="c.id" class="border-t">
            <td class="py-2">{{ c.prenom }} {{ c.nom }}</td>
            <td>{{ c.telephone }}</td>
            <td>{{ formatFCFA(c.solde) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>