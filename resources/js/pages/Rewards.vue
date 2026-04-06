<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Gift, Wallet, Flame, AlertCircle, ArrowRight, CheckCircle2 } from 'lucide-vue-next';
import MobileLayout from '@/layouts/MobileLayout.vue';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);

const props = defineProps<{
    userPoints: number;
}>();

const pointValue = 10; // 1 point = 10 FCFA
const cashValue = computed(() => props.userPoints * pointValue);

const form = useForm({
    amount: 500, // min 500 FCFA
    phone: '',
    provider: 'om'
});

const submitCashout = () => {
    form.post(route('rewards.cashout'), {
        preserveScroll: true,
        onSuccess: () => form.reset('phone', 'amount')
    });
};
</script>

<template>
    <Head title="Mes Récompenses" />

    <MobileLayout title="Récompenses">
        <!-- En-tête Points -->
        <div class="px-5 pt-6 pb-24 space-y-6">
            <div class="bg-gradient-to-br from-yellow-400 to-amber-600 rounded-3xl p-6 text-white shadow-xl shadow-amber-500/20 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-30 text-white">
                    <Gift :size="120" />
                </div>
                <div class="relative z-10">
                    <p class="text-amber-100 font-bold tracking-widest uppercase text-xs mb-1">Mon Solde</p>
                    <h2 class="text-4xl font-black mb-2">{{ userPoints }} <span class="text-lg font-bold">PTS</span></h2>
                    <p class="text-sm border-t border-white/20 pt-2 mt-2 inline-block">
                        Équivalent à environ <strong>{{ cashValue.toLocaleString() }} FCFA</strong>
                    </p>
                </div>
            </div>

            <!-- Succès -->
            <div v-if="flashMessage" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800 rounded-2xl">
                <CheckCircle2 class="shrink-0" />
                <p class="text-sm font-bold">{{ flashMessage }}</p>
            </div>
            
            <div v-if="form.errors.amount" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-2xl">
                <AlertCircle class="shrink-0" />
                <p class="text-sm font-bold">{{ form.errors.amount }}</p>
            </div>

            <!-- Option 1 : Gaz -->
            <AppCard class="border border-blue-100 dark:border-blue-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full bg-blue-500"></div>
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/40 rounded-2xl text-blue-600 dark:text-blue-400">
                        <Flame :size="24" />
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-lg">Réduction sur le Gaz</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-3">
                            Utilisez vos points directement lors de la confirmation d'une livraison de gaz pour réduire la facture finale.
                        </p>
                        <Link :href="route('order.create')" class="text-sm font-black text-blue-600 dark:text-blue-400 flex items-center gap-1 hover:gap-2 transition-all">
                            Commander du Gaz <ArrowRight :size="16" />
                        </Link>
                    </div>
                </div>
            </AppCard>

            <!-- Option 2 : Retrait Mobile Money -->
            <AppCard class="border border-easygas-green/20 dark:border-easygas-green/10 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-easygas-green/10 dark:bg-easygas-green/20 rounded-2xl text-easygas-green">
                        <Wallet :size="24" />
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white text-lg">Retrait Mobile Money</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Transférez vos gains en argent réel.</p>
                    </div>
                </div>

                <form @submit.prevent="submitCashout" class="space-y-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-2 gap-3">
                        <div 
                            @click="form.provider = 'om'" 
                            :class="['p-3 rounded-xl border flex flex-col items-center justify-center cursor-pointer transition-colors text-center', form.provider === 'om' ? 'bg-orange-50 border-orange-500 text-orange-700 dark:bg-orange-900/30 dark:border-orange-500 dark:text-orange-300' : 'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 text-gray-500']"
                        >
                            <span class="font-black">Orange Money</span>
                        </div>
                        <div 
                            @click="form.provider = 'momo'" 
                            :class="['p-3 rounded-xl border flex flex-col items-center justify-center cursor-pointer transition-colors text-center', form.provider === 'momo' ? 'bg-yellow-50 border-yellow-500 text-yellow-700 dark:bg-yellow-900/30 dark:border-yellow-500 dark:text-yellow-300' : 'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 text-gray-500']"
                        >
                            <span class="font-black">MTN MoMo</span>
                        </div>
                    </div>

                    <AppInput
                        id="phone"
                        v-model="form.phone as any"
                        type="tel"
                        label="Numéro de téléphone"
                        placeholder="Ex: 6XXXXXXXX"
                        :error="form.errors.phone"
                        required
                    />

                    <div>
                        <AppInput
                            id="amount"
                            v-model="form.amount as any"
                            type="number"
                            min="500"
                            step="100"
                            label="Montant à retirer (FCFA)"
                            placeholder="Min. 500 FCFA"
                            :error="form.errors.amount"
                            required
                        />
                        <p class="text-[10px] text-gray-400 mt-1 pl-1">Coût : {{ Math.ceil((form.amount || 0) / pointValue) }} PTS</p>
                    </div>

                    <AppButton type="submit" variant="primary" class="w-full mt-2" :loading="form.processing" :disabled="userPoints < Math.ceil((form.amount || 0) / pointValue) || form.amount < 500">
                        Initier le Retrait
                    </AppButton>
                </form>
            </AppCard>

        </div>
    </MobileLayout>
</template>
