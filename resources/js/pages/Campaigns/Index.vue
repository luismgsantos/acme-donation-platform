<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { CampaignsResponse, Campaign } from '@/types';

// Define props with imported types
defineProps<{
  campaigns: CampaignsResponse;
}>();

// A regular function, not a computed — because it accepts arguments
function campaignDonationAmount(campaign: Campaign): number {
  return campaign.donations?.reduce((total, donation) => total + donation.amount, 0) || 0;
}
</script>

<template>
  <Head title="Campaigns" />

  <AppLayout>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">All Campaigns</h1>
        <Link href="/campaigns/create" class="text-primary font-medium hover:underline">
          Create Campaign +
        </Link>
      </div>

      <div v-if="campaigns.data.length > 0" class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="campaign in campaigns.data"
          :key="campaign.id"
          class="rounded-xl border border-border p-4 hover:shadow-lg transition"
        >
          <h2 class="text-lg font-semibold truncate">{{ campaign.title }}</h2>
          <p class="text-sm text-muted-foreground line-clamp-2">{{ campaign.description }}</p>

          <p class="text-sm mt-2 font-medium">
            Goal: ${{ campaign.goal_amount?.toLocaleString() }}
          </p>

          <p class="text-sm mt-1 font-medium text-green-600">
            Raised: ${{ campaignDonationAmount(campaign).toLocaleString() }}
          </p>

          <p class="text-xs text-muted-foreground mt-1">By {{ campaign.creator?.name }}</p>

          <Link
            :href="`/campaigns/${campaign.id}`"
            class="mt-3 inline-block text-primary hover:underline text-sm"
          >
            View Campaign →
          </Link>
        </div>
      </div>

      <div v-else class="text-center text-muted-foreground py-12">
        <p class="text-lg font-medium">No campaigns found.</p>
        <p class="my-2">You can create a new one though.</p>
        <Link href="/campaigns/create">
          <Button>Create Campaign +</Button>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
