import axios from 'axios';

export async function createCampaign(data: { title: string; description: string; goal_amount: number }) {
    return axios.post(route('api.campaigns.store'), data);
}

export async function getCampaigns() {
    return axios.get(route('api.campaigns.index'));
}

export async function getCampaign(id: number) {
    return axios.get(route('api.campaigns.show', id));
}
