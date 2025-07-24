<?php

namespace App\Jobs;

use App\Traits\WhatsappTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessageGoPremium implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WhatsappTrait;

    protected $email;
    protected $phone;
    protected $name;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $phone, $name)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name = $this->name;
        $email = $this->email;
        $phone = $this->phone;

        $message = "Yei, Terima kasih kak $name sudah berkenan untuk upgrade premium Aplikasi Randu. Doa yang banyak dan tulus semoga bisnis kakak makin bertumbuh, omset lebih banyak, dan keuntungannya makin meningkat. Amin.\n\n";
        $message .= "Karena kak $name udah upgrade premium, maka kakak berhak mendapatkan fasilitas buka cabang bisnis juga dashboard Owner.\n\n";
        $message .= "Dashboard owner digunakan untuk membuka cabang bisnis baru dan memantau semua laporan bisnis dari berbagai unit bisnis dalam 1 aplikasi. Dashboard Randu Owner bisa diakses melalui\n";
        $message .= "Link: https://owner.randu.co.id\n";
        $message .= "Email Login: $email\n";
        $message .= "Password: Sama dengan Password Akun Randu App\n\n";
        $message .= "Dashboard owner juga bisa diakses melalui Aplikasi Randu - Owner Mobile yang bisa di download di\n";
        $message .= "Versi Android: https://play.google.com/store/apps/dev?id=686771859564599631\n";
        $message .= "Versi Apple IOS: https://apps.apple.com/us/developer/cv-momentum-bertumbuh-indonesia/id1746660363\n\n";
        $message .= "Tutorial membuka cabang & dashboard owner bisa dilihat di https://www.youtube.com/playlist?list=PLJykD7D6qy_UXNYK35RCj19M_EHRkgLGv\n\n";
        $message .= "Sayangku Padamu Selalu,\n";

        // Using the trait's method instead of direct HTTP call
        $this->sendWhatsappMessage($phone, $message);
    }
}
