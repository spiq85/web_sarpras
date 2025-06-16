<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DetailPengembalian; // Import model DetailPengembalian
use App\Models\User; // Import model User

class NewPengembalianNotification extends Notification
{
    use Queueable;

    public $detailPengembalian;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DetailPengembalian $detailPengembalian)
    {
        $this->detailPengembalian = $detailPengembalian->loadMissing(['user', 'barang']); // Eager load user dan barang
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
        // return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Contoh jika Anda ingin mengirim email
        $url = url('/admin/pengembalian/' . $this->detailPengembalian->id_detail_pengembalian); // Ganti dengan URL dashboard admin Anda
        $userName = $this->detailPengembalian->user->name ?? 'Pengguna';
        $itemName = $this->detailPengembalian->barang->nama_barang ?? 'Barang tidak diketahui';
        $itemQuantity = $this->detailPengembalian->jumlah ?? 0;

        return (new MailMessage)
                    ->subject('Pengajuan Pengembalian Baru')
                    ->greeting('Halo Admin,')
                    ->line("Ada pengajuan pengembalian baru dari {$userName}.")
                    ->line("Detail: {$itemQuantity}x {$itemName}.")
                    ->action('Lihat Pengembalian', $url)
                    ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $userName = $this->detailPengembalian->user->name ?? 'Pengguna Tidak Diketahui';
        $itemName = $this->detailPengembalian->barang->nama_barang ?? 'Barang Tidak Diketahui';
        $itemQuantity = $this->detailPengembalian->jumlah ?? 0;

        return [
            'pengembalian_id' => $this->detailPengembalian->id_detail_pengembalian,
            'user_id' => $this->detailPengembalian->users_id,
            'returner_name' => $userName,
            'item_name' => $itemName,
            'quantity' => $itemQuantity,
            'type' => 'pengembalian_baru',
            'message' => "{$userName} mengajukan pengembalian {$itemQuantity}x {$itemName}.",
            'url' => '/admin/pengembalian/' . $this->detailPengembalian->id_detail_pengembalian, // URL untuk admin
        ];
    }
}