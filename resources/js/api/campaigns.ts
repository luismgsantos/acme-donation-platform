import { Campaign } from '@/types';
import axios from 'axios';

export async function createCampaign(data: Campaign) {
    return axios.post(route('api.campaigns.store'), data);
}

export async function getCampaigns() {
    return axios.get(route('api.campaigns.index'));
}

export async function getCampaign(id: number) {
    return axios.get(route('api.campaigns.show', id));
}

export async function updateCampaign(id: number, data: Campaign) {
    return axios.put(route('api.campaigns.update', id), data);
}

export async function deleteCampaign(id: number) {
    return axios.delete(route('api.campaigns.destroy', id));
}
