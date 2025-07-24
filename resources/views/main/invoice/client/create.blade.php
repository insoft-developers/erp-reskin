<form method="POST" id="formCreate" action="{{ route('invoice.client.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Client Baru</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name') }}" placeholder="" required>
                    </input>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="email">Email </label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="">
                    </input>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone') }}" placeholder="+62" required>
                    </input>
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="mobile">Mobile </label>
                    <input type="number" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                        name="mobile" value="{{ old('mobile') }}" placeholder="+62">
                    </input>
                    @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="fax">Fax </label>
                    <input type="number" class="form-control @error('fax') is-invalid @enderror" id="fax" name="fax"
                        value="{{ old('fax') }}" placeholder="+62">
                    </input>
                    @error('fax')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="address">Address </label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                        rows="3">{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>

<script>
    document.getElementById('formCreate').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const form = event.target;
        const formData = new FormData(form);
        const url = form.action;
    
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.errors) {
                let errorMessages = '';
                for (const [field, messages] of Object.entries(data.errors)) {
                    errorMessages += messages.join('<br>') + '<br>';
                }
    
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessages
                });
            }else if(!data.status){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
        });
    });
</script>