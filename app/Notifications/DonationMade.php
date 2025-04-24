<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationMade extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Donation $donation) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getMailSubject($notifiable))
            ->line($this->getMailHeader($notifiable))
            ->action('View Campaign', url("/campaigns/{$this->donation->campaign->id}"))
            ->line('Thank you for supporting our initiatives!');
    }

    private function getMailSubject($notifiable): string
    {
        $campaign = $this->donation->campaign;
        $donor = $this->donation->user;

        return $notifiable->id === $donor->id
            ? 'Thank You for Your Donation!'
            : 'Your Campaign Received a Donation!';
    }

    private function getMailHeader($notifiable): string
    {
        $campaign = $this->donation->campaign;
        $donor = $this->donation->user;

        return $notifiable->id === $donor->id
            ? "You donated to the campaign: \"{$campaign->title}\""
            : "{$donor->name} just donated to your campaign: \"{$campaign->title}\"";
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->getMailHeader($notifiable),
            'campaign_id' => $this->donation->campaign_id,
            'donation_amount' => $this->donation->amount,
            'donor_id' => $this->donation->user_id,
            'campaign_creator_id' => $this->donation->campaign->user_id,
        ];
    }
}
