<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="panelsStayOpen-headingFive">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
            Pengaturan Checkout Lewat WhatsApp
        </button>
    </h2>
    <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
        <div class="accordion-body">
            <div class="mb-3 row">
                <div class="form-check shipping" data-method="Chekout-whatsapp">
                    <input class="form-check-input" type="checkbox" id="checkout-whatsapp">
                    <label class="form-check-label" for="checkout-whatsapp">Aktifkan Checkout lewat WhatsApp</label>
                  </div>
                  
                  
            </div>
<div class="mb-3 row">
    <div class="form-group">
        <label>Nomor WhatsApp Admin (62):</label>
        <input 
            type="text" 
            placeholder="6281390909090" 
            name="whatsapp_number" 
            id="whatsapp_number" 
            class="form-control" 
            value="{{ $setting ? $setting->whatsapp_number : '' }}"
        >
    </div>
</div>

<script>
    const waInput = document.getElementById('whatsapp_number');

    // Isi "62" jika diklik dan kosong
    waInput.addEventListener('focus', function () {
        if (this.value.trim() === '') {
            this.value = '62';
        }
    });

    // Ganti "0" di awal dengan "62" saat input berubah
    waInput.addEventListener('input', function () {
        if (this.value.startsWith('0')) {
            this.value = '62' + this.value.slice(1);
        }
    });

    // Cek saat halaman dimuat (untuk autofill browser)
    window.addEventListener('DOMContentLoaded', function () {
        if (waInput.value.startsWith('0')) {
            waInput.value = '62' + waInput.value.slice(1);
        }
    });
</script>

<div class="mb-3 row">
    <div class="form-group">
        <label>Template Text Pesanan:</label>
 <small class="text-muted d-block">Gunakan kode [Detail-Order], maka konsumen akan mengirim otomatis rekap pesanan.</small>
        <textarea 
            placeholder="ketik Hai Kak saya mau order [Detail-Order]" 
            name="template_order_info" 
            id="template-order-info" 
            class="form-control" 
            style="height: 90px;"
        >{{ $setting && $setting->template_order_info ? $setting->template_order_info : '' }}</textarea>    
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('template-order-info');
        const defaultText = `Halo, saya mau Order:\n\n[Detail-Order]\n\nMohon di Proses.`;

        if (textarea.value.trim() === '') {
            textarea.value = defaultText;
        }
    });
</script>


            
        </div>
    </div>
</div>
