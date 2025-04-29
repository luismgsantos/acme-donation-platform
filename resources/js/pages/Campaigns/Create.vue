<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { createCampaign } from '@/api/campaigns';

const title = ref('');
const description = ref('');
const goalAmount = ref('');

const errors = ref<{ title?: string; description?: string; goal_amount?: string }>({});
const isLoading = ref(false);

const submit = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        const response = await createCampaign({
            title: title.value,
            description: description.value,
            goal_amount: parseFloat(goalAmount.value),
        });

        window.location.href = `/campaigns/${response.data.data.id}`;
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Start a New Campaign" />

    <AppLayout>
        <div class="flex items-center justify-center min-h-screen">
            <div class="w-full max-w-xl space-y-6 rounded-xl border border-border p-6 shadow-sm m-4 justify-self-auto">
                <h1 class="text-2xl font-bold">Start a New Campaign</h1>
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input id="title" type="text" required autofocus :tabindex="1" autocomplete="title"
                                v-model="title" placeholder="E.g: Save a rubber duck today" />
                            <InputError :message="errors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Input id="description" type="text" required autofocus :tabindex="2"
                                autocomplete="description" v-model="description"
                                placeholder="E.g: Thousands of rubber ducklings live in fear because of developers..." />
                            <InputError :message="errors.description" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="goal_amount">Amount</Label>
                            <Input id="goal_amount" type="number" required autofocus :tabindex="3" min="0"
                                autocomplete="goal_amount" v-model="goalAmount"
                                placeholder="How much money do you want to raise?" />
                            <InputError :message="errors.goal_amount" />
                        </div>
                    </div>

                    <Button type="submit" :disabled="isLoading">
                        <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin" />
                        Create Campaign
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
