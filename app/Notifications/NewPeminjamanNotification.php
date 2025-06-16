<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Peminjaman; // Import model Peminjaman
use App\Models\DetailPeminjaman; // Import model DetailPeminjaman
use App\Models\User; // Import model User

class NewPeminjamanNotification extends Notification
{
    use Queueable;

    public $peminjaman;
    public $detailPeminjaman; // Jika ingin menyertakan detail

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
        // Optionally load detail if not already eager-loaded
        $this->detailPeminjaman = $peminjaman->detail ?? DetailPeminjaman::find($peminjaman->id_detail_peminjaman);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Kita akan simpan di database
        // return ['database', 'mail']; // Bisa juga tambahkan 'mail' jika ingin kirim email
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
        $url = url('/admin/peminjaman/' . $this->peminjaman->id_peminjaman); // Ganti dengan URL dashboard admin Anda
        $userName = $this->peminjaman->user->name ?? 'Pengguna';
        $itemName = $this->detailPeminjaman->barang->nama_barang ?? 'Barang tidak diketahui';
        $itemQuantity = $this->detailPeminjaman->jumlah ?? 0;

        return (new MailMessage)
                    ->subject('Pengajuan Peminjaman Baru')
                    ->greeting('Halo Admin,')
                    ->line("Ada pengajuan peminjaman baru dari {$userName}.")
                    ->line("Detail: {$itemQuantity}x {$itemName} untuk keperluan \"{$this->detailPeminjaman->keperluan}\".")
                    ->action('Lihat Peminjaman', $url)
                    ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     * Ini adalah data yang akan disimpan di tabel 'notifications'
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Memastikan relasi dimuat untuk menghindari error jika belum di-load
        $userName = $this->peminjaman->user->name ?? 'Pengguna Tidak Diketahui';
        $itemName = $this->detailPeminjaman->barang->nama_barang ?? 'Barang Tidak Diketahui';
        $itemQuantity = $this->detailPeminjaman->jumlah ?? 0;

        return [
            'peminjaman_id' => $this->peminjaman->id_peminjaman,
            'user_id' => $this->peminjaman->users_id,
            'borrower_name' => $userName,
            'item_name' => $itemName,
            'quantity' => $itemQuantity,
            'type' => 'peminjaman_baru',
            'message' => "{$userName} mengajukan peminjaman {$itemQuantity}x {$itemName}.",
            'url' => '/admin/peminjaman/' . $this->peminjaman->id_peminjaman, // URL untuk admin
        ];
    }
}