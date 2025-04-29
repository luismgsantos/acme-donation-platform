import axios from 'axios';

export async function donateToCampaign(campaignId: number, amount: number) {
    return axios.post(route('api.campaigns.donate', campaignId), {
        amount,
    });
}
