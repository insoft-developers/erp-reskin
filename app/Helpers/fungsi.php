<?php

use App\Http\Controllers\Controller;
use App\Models\MlAccount;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

if (!function_exists('ribuan')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function ribuan($angka)
    {
        $angka_angka = number_format($angka);
        $angka_baru = str_replace(",",".", $angka_angka);
        return $angka_baru;
    }
}


if (!function_exists('limitList')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function limitList($limit = null)
    {
        $limit = !empty($limit) ? $limit:10;

        return $limit;
    }
}

if (!function_exists('validationPhoneNumber')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function validationPhoneNumber($value)
    {
        if ($value[0] === '0') {
            $value = '62' . substr($value, 1);
        }

        return $value;
    }
}

if (!function_exists('notifications')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function notifications($limit = 30)
    {
        $data = Notification::orderBy('id', 'desc')->limit($limit)->get();

        return $data;
    }
}

if (!function_exists('markAsRead')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function markAsRead()
    {
        return session('read_all_notification') ?? false;
    }
}

if (!function_exists('bulan')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function bulan()
    {
        $data = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return $data;
    }
}

if (!function_exists('tahun')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function tahun()
    {
        // 10 TAHUN KEBELAKANG COLLECT TO ARRAY
        $data = collect(range(date('Y'), date('Y') - 10))->toArray();

        return $data;
    }
}

if (!function_exists('paymentMethodCast')) {
    /**
     * Greeting a person
     *
     * @param  string $person Name
     * @return string
     */
    function paymentMethodCast($value)
    {
        // kas
        // bank-bca
        // bank-bni
        // bank-mandiri
        // bank-bri
        // bank-lain
        // randu-wallet
        // piutang-marketplace
        // piutang-cod
        // piutang-usaha
        switch (true) {
            case $value == 'kas':
                return 'Cash';
            case $value == 'bank-bca':
                return 'Bank BCA';
            case $value == 'bank-bni':
                return 'Bank BNI';
            case $value == 'bank-mandiri':
                return 'Bank Mandiri';
            case $value == 'bank-bri':
                return 'Bank BRI';
            case $value == 'bank-lain':
                return 'Bank Lainnya';
            case $value == 'randu-wallet':
                return 'Payment Gateway';
            case $value == 'piutang-marketplace':
                return 'Marketplace';
            case $value == 'piutang-cod':
                return 'Cash On Delivery';
            case $value == 'piutang-usaha':
                return 'Accounts Receivable';
            default:
                return $value;
        };
        
    }
}

if (!function_exists('customNumberFormat')) {
    function customNumberFormat($number) {
        // Jika angkanya bulat, gunakan number_format tanpa desimal
        if (floor($number) == $number) {
            return number_format($number, 0, ',', '.');
        }
        
        // Jika angka memiliki desimal, format dengan 3 tempat desimal
        $formatted = number_format($number, 3, ',', '.');
        
        // Menghapus desimal jika nol
        return rtrim(rtrim($formatted, '0'), ',');
    }
}

if (!function_exists('statusPenjualan')) {
    function statusPenjualan($number) {
        switch ($number) {
            case 0:
                return 'Pending';
            case 1:
                return 'Process';
            case 2:
                return 'Cooking/Packing';
            case 3:
                return 'Shipped';
            case 4:
                return 'Complete';
            case 5:
                return 'Canceled';
        }
    }
}

if (!function_exists('notifPopup')) {
    function notifPopup() {
        $data = Notification::where('show_popup', 1)->first();

        return $data;
    }
}

if (!function_exists('user')) {
    function user() {
        $data = MlAccount::find(session('id'));
        
        return $data;
    }
}

if (!function_exists('userId')) {
    function userOwnerId() {
        $global = new Controller();
        $userId = session('id') ?? Auth::user()->id;
        $userId = $global->get_owner_id($userId);
        
        return $userId;
    }
}

if (!function_exists('userId')) {
    function dayFormatEn($value) {
        if ($value == 'Minggu') {
            return 'Sun';
        } elseif ($value == 'Senin') {
            return 'Mon';
        } elseif ($value == 'Selasa') {
            return 'Tue';
        } elseif ($value == 'Rabu') {
            return 'Wed';
        } elseif ($value == 'Kamis') {
            return 'Thu';
        } elseif ($value == 'Jumat') {
            return 'Fri';
        } elseif ($value == 'Sabtu') {
            return 'Sat';
        }
    }
}