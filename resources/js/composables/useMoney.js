import { computed } from 'vue'

export const formatFCFA = (n) =>
  new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'

// 9 chiffres, préfixes sénégalais courants
export const telValide = (tel) =>
  /^(77|78|76|70|75)\d{7}$/.test(String(tel).replace(/\s/g, ''))

export function useTransactions(transactions, moi) {
  const liste = computed(() =>
    transactions.value.map((t) => {
      const sortant = t.type === 'transfert' && t.expediteur_id === moi
      return {
        ...t,
        sens: sortant ? 'sortant' : 'entrant',
        libelle:
          t.type === 'credit'
            ? 'Crédit reçu du distributeur'
            : sortant
              ? `Envoi à ${t.destinataire.prenom} ${t.destinataire.nom}`
              : `Reçu de ${t.expediteur.prenom} ${t.expediteur.nom}`,
        date: new Date(t.created_at).toLocaleString('fr-FR'),
      }
    })
  )
  return { liste }
}