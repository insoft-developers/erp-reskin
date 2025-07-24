@extends('master')
@section('style')
<style>
    .image-container {
      position: relative;
      width: 100%;
      padding-top: 100%; /* 1:1 Aspect Ratio */
    }
    .main-image, .overlay-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .overlay-image {
      width: 25%; /* Adjust as needed */
      height: 25%; /* Adjust as needed */
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      margin-top:10px;
      opacity: 0.9;
    }
    .text-container {
      text-align: center;
      margin-top: 10px;
    }
    .button-container {
      display: flex;
      justify-content: center;
      margin-top: 10px;
      font-size:20px
    }

@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
        color: #000;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        color: #000;

        margin-left:-20px;
    }
    .print-area .col-4-print {
        width:33%;
        float:left;
    }
    @page {
        size: A4; /* You can set any page size here */
        margin: 20mm;
    }
}
</style>
@endsection
@section('content')
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10"> Qrcode > Print QR Code </h5>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Print QR code</h5>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="container mtop30 main-box">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!-- <button class="btn btn-primary" onclick="printSpecificArea()"><i class="fa fa-print"></i>&nbsp; Print QR Code</button> -->
                                        </div>
                                        <div class="col-md-4">

                                        </div>
                                    </div>

                                    <div class="mtop30"></div>
                                    <div class="print-area">
                                        <div class="container">

                                            <div class="row" id="QrData">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mtop50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    "use strict";
    $(document).ready(function () {
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "{{route('ajax-get-qrcode')}}",
            type: 'GET',
        })
        .done(function(response) {
            const data = response.data
            const company = "{{$company ? $company->branch_name : 'Default Company'}}"
            data.forEach((item, index)=>{
                // Create a card element for each object
                var card = $(`
                <div class="col-sm-3 col-md-3 col-lg-3 text-center border">
                    <div class="capture" id="${item.qr_id}">
                        <div class="image-container">
                            <img src="data:image/png;base64, ${item.qr_image}" alt="QR Code" class="main-image mt-2">
                            <img src="/template/main/images/randu.png" alt="Icon" class="overlay-image">
                        </div>
                        <div class="text-container">
                            <h5 class="mt-4">${company}</h5>
                            <h5 class="mt-2">Meja ${item.no_meja}</h5>
                        </div>
                    </div>
                    <div class="button-container mt-3 mb-3">
                        <button type="button" class="btn btn-success rounded-pill download-btn" data-target="#${item.qr_id}">DOWNLOAD QR</button>
                    </div>
                </div>
                `)
                // Append card to dataContainer
                $('#QrData').append(card);
            })
        })
        .fail(function() {
            swal.fire('Oops...', 'Something went wrong with data !', 'error');
        });
        attachDownloadHandlers();
        function attachDownloadHandlers() {
          console.log('Attaching download handlers');
          $('#QrData').on('click', '.download-btn', function() {
            console.log('Button clicked');
            const targetId = $(this).data('target');
            const targetElement = $(targetId)[0];
            html2canvas(targetElement).then(function(canvas) {
              const link = document.createElement('a');
              link.href = canvas.toDataURL('image/png');
              link.download = `${targetId.replace('#', '')}.png`;
              link.click();
            }).catch(function(error) {
              console.error('html2canvas error:', error);
            });
          });
        }
    });
    function printSpecificArea() {
            var printContents = $('.print-area').clone();
            var originalContents = $('body').clone();

            $('body').html(printContents);

            // Wait for images and content to load before printing
            $('body').imagesLoaded(function() {
                window.print();
                $('body').html(originalContents);
            });
        }

        // jQuery plugin to wait for all images to be loaded
        $.fn.imagesLoaded = function(callback) {
            var $images = this.find('img');
            var imgLoaded = 0;
            var imgCount = $images.length;

            if (imgCount === 0) {
                callback();
            }

            $images.each(function() {
                if (this.complete) {
                    imgLoaded++;
                    if (imgLoaded === imgCount) {
                        callback();
                    }
                } else {
                    $(this).on('load', function() {
                        imgLoaded++;
                        if (imgLoaded === imgCount) {
                            callback();
                        }
                    });
                }
            });

            return this;
        };

</script>
@endsection
