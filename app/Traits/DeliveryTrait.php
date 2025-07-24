<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait DeliveryTrait
{
    /*
        Cara penggunaan getData:
        merujuk pada https://rajaongkir.com/dokumentasi/pro
        $dest = Destinasi endpoint yang dituju misal province, city, subdistrict
        $param1 = Parameter pertama berupa id yang merujuk pada destinasi, misal /city?id=1 akan mendapatkan data kota dengan id 1
        $param2 = Parameter kedua sebagai rujukan khususnya untuk city dan subdistrict, misal /city?province=1 akan mendapatkan list data kota dari provinsi dengan id 1
        Jika keseluruhan diisi maka akan mendapatkan data spesifik misal /city?id=1&province=1 maka akan mendapatkan data kota dengan id 1 dari provinsi dengan id 1

    */
    public function getData($dest, $param1 = "", $param2 = "")
    {
        $deliveryApi = env('RAJAONGKIR_API_KEY');
        if ($dest == 'city') {
            if ($param1 != '') {
                $param = '?id=' . $param1 . '&province=' . $param2;
            } else {
                $param = '?province=' . $param2;
            }
        } else if ($dest == 'subdistrict') {
            if ($param1 != '') {
                $param = '?id=' . $param1 . '&city=' . $param2;
            } else {
                $param = '?city=' . $param2;
            }
        } else {
            $param = "";
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/" . $dest . $param,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: " . $deliveryApi
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
            $provinces = $data['rajaongkir']['results'];
            return $provinces;
        }
    }
    /*
        Cara Penggunaan getCost:
        $data = [
            "origin" => $request->origin,
            "originType" => $request->originType,
            "destination" => $request->destination,
            "destinationType" => $request->destinationType,
            "weight" => $request->weight,
            "courier" => $request->courier
        ];
        Origin = berasal dari pengaturan alamat di pengaturan storefront
        Destination = berasal dari inputan user yang melakaukan pemesanan delivery
        Destination Type di set ke subdistict supaya lebih luas.
        Raja ongkir hanya sampai kecamatan tidak sampai kelurahan.
    */
    public function getCost($data)
    {
        $deliveryApi = env('RAJAONGKIR_API_KEY');
        $curl = curl_init();
        $origin = $data['origin'];
        $originType = $data['originType'];
        $destination = $data['destination'];
        $destinationType = $data['destinationType'];
        $weight = $data['weight'];
        $courier = $data['courier'];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=$origin&originType=$originType&destination=$destination&destinationType=$destinationType&weight=$weight&courier=$courier",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: " . $deliveryApi
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
            // Log::debug($data);
            if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] != 200) {
                return [
                    'status' => 'error',
                    'message' => $data['rajaongkir']['status']['description']
                ];
            }
            $provinces = $data['rajaongkir']['results'];
            return [
                'status' => 'success',
                'data' => $provinces
            ];
        }
    }
}
