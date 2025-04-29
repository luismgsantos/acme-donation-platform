<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { donateToCampaign } from '@/api/donations';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';

const props = defineProps<{
    campaign: {
        data: {
            id: number;
            title: string;
            description: string | null;
            goal_amount: number;
            created_at: string;
            creator: {
                id: number;
                name: string;
            };
            donations: Array<{
                id: number;
                user_id: number;
                amount: number;
            }>;
        }
    };
}>();

const amount = ref('');
const errors = ref<{ amount?: string }>({});
const isLoading = ref(false);

const submit = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        await donateToCampaign(props.campaign.data.id, parseFloat(amount.value));
        amount.value = '';
        window.location.reload(); // reload page to show updated donations
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        isLoading.value = false;
    }
};

const totalDonated = computed(() =>
    props.campaign.data.donations?.reduce((sum, d) => sum + d.amount, 0),
);
</script>

<template>
    <Head :title="campaign.data.title" />

    <AppLayout :breadcrumbs="[{ title: 'Campaigns', href: '/campaigns' }]">
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <div class="rounded-xl border p-6 shadow">
                <h1 class="text-3xl font-bold">{{ campaign.data.title }}</h1>
                <p class="mt-2 text-gray-600">{{ campaign.data.description }}</p>

                <div class="mt-4 space-y-1">
                    <p><strong>Goal:</strong> ${{ campaign.data.goal_amount }}</p>
                    <p><strong>Raised:</strong> ${{ totalDonated.toFixed(2) }}</p>
                    <p><strong>Creator:</strong> {{ campaign.data.creator.name }}</p>
                    <p class="text-sm text-muted-foreground">
                        Created: {{ (new Date(campaign.data.created_at)).toUTCString() }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl border p-6 shadow">
                <h2 class="text-xl font-semibold">Donate to this campaign</h2>
                <form @submit.prevent="submit" class="mt-4 space-y-4">
                    <div class="grid gap-2">
                        <Label for="amount">Amount (€)</Label>
                        <Input
                            id="amount"
                            type="number"
                            required
                            autofocus
                            :tabindex="1"
                            min="1"
                            step="any"
                            autocomplete="amount"
                            v-model="amount"
                            placeholder="How much money will you donate to help this campaign?"
                        />
                        <InputError :message="errors.amount" />
                    </div>
                    <Button type="submit" :disabled="isLoading">
                        <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin" />
                        Donate
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
