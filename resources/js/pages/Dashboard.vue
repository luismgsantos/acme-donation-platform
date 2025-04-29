<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Donation, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { ref, onMounted } from 'vue';
import { getCampaigns } from '@/api/campaigns';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: '/campaigns',
    },
];

const topDonatorsData = ref<any>({
    labels: [],
    datasets: [],
});

const fetchTopDonators = async () => {
    const { data: responseData } = await getCampaigns();
    const campaignsData = responseData.data;


    const allDonations = campaignsData.flatMap((campaign: any) => campaign.donations);


    const donationsByUser: Record<number, { name: string, total: number }> = {};

    allDonations.forEach((donation: Donation) => {
        if (!donationsByUser[donation.donor.id]) {
            donationsByUser[donation.donor.id] = {
                name: donation.donor.name,
                total: 0,
            };
        }

        donationsByUser[donation.donor.id].total += donation.amount;
    });


    const topDonators = Object.values(donationsByUser)
        .sort((a, b) => b.total - a.total)
        .slice(0, 5);


    topDonatorsData.value = {
        labels: topDonators.map(d => d.name),
        datasets: [
            {
                label: 'Total Donated (€)',
                data: topDonators.map(d => d.total),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            },
        ],
    };
};

onMounted(() => {
    fetchTopDonators();
});
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="border rounded-xl p-4 shadow">
                <h2 class="text-2xl font-bold text-center mb-6">Top 5 Donators</h2>
                <Bar :data="topDonatorsData" />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Center the chart title */
h2 {
    text-align: center;
}
</style>
