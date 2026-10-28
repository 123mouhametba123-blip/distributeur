<script setup>
import { computed, toRef } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { formatFCFA, telValide, useTransactions } from '@/composables/useMoney'

const props = defineProps({ solde: [Number, String], transactions: Array, moi: Number, clients: Array  })
const { liste } = useTransactions(toRef(props, 'transactions'), props.moi)

const form = useForm({ telephone: '', montant: '' })

const transfertBloque = computed(
  () =>
    (!!form.telephone && !telValide(form.telephone)) ||
    Number(form.montant) > Number(props.solde)
)
const peutEnvoyer = computed(
  () => telValide(form.telephone) && Number(form.montant) > 0 && !transfertBloque.value
)
const envoyer = () => form.post('/transfert', { onSuccess: () => form.reset() })
</script>

<template>
  <div class="max-w-2xl mx-auto p-6 space-y-6">
    <div class="flex justify-between">
        <!-- bienvenu {{ c.nom }} -->
      <h1 class="text-2xl font-bold">Mon compte</h1>
      <button @click="router.post('/logout')" class="text-red-600">Déconnexion</button>
    </div>

    <div class="bg-blue-600 text-white rounded-xl p-6">
      <p class="text-sm opacity-80">Solde</p>
      <p class="text-3xl font-bold">{{ formatFCFA(solde) }}</p>
    </div>

    <form @submit.prevent="envoyer" class="border p-4 rounded-xl space-y-3 bg-white">
      <h2 class="font-semibold">Envoyer de l'argent</h2>
      <input v-model="form.telephone" placeholder="Numéro du destinataire" class="w-full border p-2 rounded" />
      <input v-model="form.montant" type="number" min="1" placeholder="Montant" class="w-full border p-2 rounded" />
      <button :disabled="!peutEnvoyer || form.processing" class="w-full bg-green-600 text-white p-2 rounded disabled:opacity-40">
        Envoyer
      </button>
    </form>

    <div>
      <h2 class="font-semibold mb-2">Historique</h2>
      <p v-if="!liste.length" class="text-gray-500">Aucune transaction pour le moment.</p>
      <ul class="divide-y">
        <li v-for="t in liste" :key="t.id" class="py-2 flex justify-between">
          <div>
            <p>{{ t.libelle }}</p>
            <p class="text-xs text-gray-500">{{ t.date }}</p>
          </div>
          <span :class="t.sens === 'entrant' ? 'text-green-600' : 'text-red-600'">
            {{ t.sens === 'entrant' ? '+' : '-' }}{{ formatFCFA(t.montant) }}
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>
