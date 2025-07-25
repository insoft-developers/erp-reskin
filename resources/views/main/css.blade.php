

<style>

    #table-jurnal_filter, #table-jurnal_length{
        display: none;
    }
    .select-month {
        width: 200px;
        margin-right: 10px;
    }

    .select-year {
        width: 200px;
    }

    .select-month2 {
        width: 200px;
        margin-right: 10px;
    }

    .select-year2 {
        width: 200px;
        margin-right: 7px;
    }

    .text-inbalance {
        position: relative;
        top: -2px;
        font-size: 14px;
        color: red;
        left: 6px;
        font-weight: 600;
    }

    .select2-container {
        z-index: 99999;
    }

    #l-image {
        width: 50px;
        position: absolute;
        left: 57%;
        top: 80%;
        z-index: 99999999 !important;
    }

    .btn-hapus-akun {
        background: red;
        color: white;
        padding-top: 7px;
        border-radius: 65px;
        padding-bottom: 7px;
        padding-left: 10px;
        padding-right: 10px;
        margin-top: 1px;
        cursor: pointer;
    }

    .m-header {
        background: #2f467a !important;
    }

    .logo-besar {
        width: 55px;
        height: 55px;
    }

    .brand-title {
        color: white;
        font-size: 17px;
        /* margin-left: 0px; */
        display: block;
        position: absolute;
        top: 24px;
        left: 95px;
    }

    .img-menu {
        width: 45px;
        height: 45px;
        position: relative;
        object-fit: cover;
        left: -14px;
    }

    .menu-text {
        position: relative;
        left: -20px;
        top: -5px;
    }

    .menu-subtitle {
        margin-left: 40px !important;
        margin-top: -20px !important;
        display: block;
        font-size: 10px !important;
        font-weight: 400 !important;

    }

    .nxl-arrow {
        margin-top: 12px !important;
    }

    .pull-right {
        float: right !important;
        position: absolute;
        right: 155px !important;
    }



    .footer {
        position: absolute;
        width: 100%;
        bottom: 0;
    }

    .mtop10 {
        margin-top: 10px !important;
    }

    .mtop20 {
        margin-top: 20px !important;
    }

    .mtop30 {
        margin-top: 30px !important;
    }

    .mtop40 {
        margin-top: 40px !important;
    }

    .mtop50 {
        margin-top: 50px !important;
    }

    .mtop60 {
        margin-top: 60px !important;
    }

    .mtop70 {
        margin-top: 70px !important;
    }

    .mtop80 {
        margin-top: 80px !important;
    }

    .mtop90 {
        margin-top: 90px !important;
    }

    .mtop100 {
        margin-top: 100px !important;
    }

    .cust-control {
        height: 36px;
        padding-top: 0;
        padding-bottom: 0;

    }

    .page-header {
        min-height: 30px !important;

    }

    .main-box {
        padding-left: 20px;
        padding-right: 20px;
    }

    .select2-selection__rendered {
        padding-top: -5px !important;
        font-size: 14px;
        padding-top: 2px !important;

    }

    .dt-length,
    .dt-search {
        display: none;
    }

    #table-supplier_wrapper .dt-search,
    #table-material_wrapper .dt-search,
    #table-purchase_wrapper .dt-search,
    #table-material-purchase_wrapper .dt-search,
    #table-inter-purchase_wrapper .dt-search,
    #table-inter-product_wrapper .dt-search,
    #table-product-list_wrapper .dt-search,
    #table-product-category_wrapper .dt-search {
        display: block !important;
    }

    #table-supplier_wrapper .dt-search #dt-search-0,
    #table-material_wrapper .dt-search #dt-search-0,
    #table-purchase_wrapper .dt-search #dt-search-0,
    #table-material-purchase_wrapper .dt-search #dt-search-0,
    #table-inter-purchase_wrapper .dt-search #dt-search-0,
    #table-inter-product_wrapper .dt-search #dt-search-0,
    #table-product-list_wrapper .dt-search #dt-search-1,
    #table-product-category_wrapper .dt-search #dt-search-0 {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        margin-bottom: 10px !important;
        font-size: 13px !important;
    }

    #table-jurnal th,
    #table-product-list th,
    #table-supplier th,
    #table-material th,
    #table-purchase th,
    #table-material-purchase th,
    #table-inter-purchase th,
    #table-inter-product th,
    #data-table th,
    #table-product-category th {
        font-size: 12px;
        border-bottom: 2px solid black;
        white-space: nowrap;
    }

    #table-profit-loss td,
    #table-profit-loss th {
        padding-top: 3px;
        padding-bottom: 3px;
    }

    #table-product-list th,
    #table-supplier th,
    #table-material th,
    #table-purchase th,
    #table-material-purchase th,
    #table-inter-purchase th,
    #data-table th,
    #table-product-list th {}

    #table-product-list td,
    #table-supplier td,
    #table-material td,
    #table-purchase td,
    #table-material-purchase td,
    #table-inter-purchase td,
    #table-inter-product td,
    #table-product-category td {

        font-size: 12px;
    }



    .date-box {

        width: 50px;
        padding: 14px 10px 14px 10px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
    }

    .del-item {
        padding-top: 10px;
        background: darkred;
        color: white;
        width: 28px;
        position: relative;
        right: -273px;
        top: -34px;
        padding-bottom: 10px;

    }

    .del-item:hover {
        opacity: 0.5 !important;
        background: orange !important;
        color: white !important;
    }

    .label-total,
    .label-debit,
    .label-kredit {
        font-weight: bold;
        margin-left: 14px;

    }

    .label-total {
        float: right;
    }

    .card-header {
        background-color: #2f467a !important;

    }

    .card-title {
        color: white !important;
    }

    .report-menu-title {
        font-size: 15px;
    }

    .report-menu-subtitle {
        font-size: 12px;
        color: darkgrey;
    }

    .menu-report-row {
        padding-top: 15px !important;
        padding-bottom: 15px !important;
        cursor: pointer !important;
    }

    .stanggal-text {
        font-weight: 600;
        display: block;
        margin-top: 6px;
        text-align: center;
    }

    #btn-submit-profit-loss {
        height: 34px;
    }

    .help-block {
        color: red;
        margin-top: 10px;
    }

    #table-trial-balance td {
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .pc-title {

        text-align: justify;
    }

    #month_to,
    #year_to {
        display: none;
    }

    .sampai-dengan {
        font-size: 16px;
        font-weight: 700;
        margin-left: 10px;
        margin-right: 10px;
        margin-top: 3px;
    }

    .product-images {
        width: 45px;
        height: 55px;
        border-radius: 3px;
        object-fit: cover;
        border: 2px solid whitesmoke;
        padding: 3px;
    }

    .dt-paging {
        float: right;
        margin-bottom: 15px !important;
        margin-top: -32px !important;
    }

    .page-link {
        font-size: 12px;
        padding: 9px;
    }

    .head-control {
        padding-top: 6px;
        padding-bottom: 6px;
        font-size: 12px;
        padding-left: 6px;
        padding-right: 6px;
    }

    .btn-container {
        display: inline-flex;
    }

    .btn-top {
        margin-right: 10px;
        width: 154px;
    }

    .btn-custom {
        font-size: 8px !important;
        width: 30px !important;
    }

    .btn-custom:hover {
        opacity: 0.5 !important;
    }

    .help-text {
        color: red;
        font-size: 10px;
    }

    textarea {
        padding-top: 5px !important;
    }

    #loading-image {
        width: 50px;
        position: absolute;
        left: 45%;
        top: 30%;
        z-index: 99999999 !important;
    }

    .loading-gambar {
        width: 50px;
        position: absolute;
        left: 45%;
        top: 30%;
        z-index: 99999999 !important;
    }


    #loading-image-upload {
        width: 50px;
        position: absolute;
        left: 45%;
        top: 30%;
        z-index: 99999999 !important;
    }

    /* .select2-container {
        z-index: 9999;
    } */
    .select2-dropdown {
        z-index: 9001;
    }

    .select2-container .select2-selection--single {
        padding: .150rem 2.25rem .375rem .45rem !important;
    }

    .select2-container .select2-selection {
        min-height: calc(1.8em + .75rem + 2px) !important;
    }

    .select2-selection__rendered {
        font-size: 14px;
        padding-top: 2px !important;
        font-weight: 400 !important;
    }

    .kartu {
        background: white;
        padding: 19px;
        border-radius: 5px;
        border: 1px solid #e3e3e3;
    }

    .cke_notifications_area {
        display: none !important;
    }

    .upload-image {
        width: 160px;
        border: 1px solid lightgrey;
        border-radius: 7px;
        margin-top: 20px;
        cursor: pointer;
    }

    .bg-beige {
        background: #f6f8ff;
    }

    .img-preview {
        width: 117px;
        height: 140px;
        object-fit: cover;
        border: 1px solid #cdcddb;
        border-radius: 5px;
        margin-bottom: 10px;
        margin-right: 10px;

    }

    .img-category {
        width: 60px;
        height: 70px;
        object-fit: cover;
        border: 1px solid #cdcddb;
        border-radius: 5px;
        margin-bottom: 10px;
        margin-right: 10px;
        cursor: pointer;

    }

    d-flex {
        display: flex !important;
    }

    .image-detail-show {
        width: 100px;
        height: 121px;
        object-fit: contain;
        border: 1px solid lightblue;
        padding: 2px;
        border-radius: 5px;
        margin-right: 5px;
        display: inline-block;
        cursor: pointer;
    }

    .chk-item {
        margin-top: 10px;
    }

    .button-product-action {
        display: inline-flex;
        margin-top: 4px;
    }

    .row {
        --bs-gutter-x: 0.5rem !important;
    }

    .btn-insoft {
        height: 34px;
        margin-top: 0px;
    }

    .select2-dropdown {
        z-index: 99999;
    }

    .stock-alert-note {
        position: relative;
        top: -21px;
    }

    .journal-images-preview {
        width: 300px;
        border-radius: 10px;
    }

    .radio-type-input {
        margin-bottom: 13px;
    }

    .sampai-dengan {
        display: block !important;
    }

    .sampai-dengan-mobile {
        display: none !important;

    }



    @media only screen and (max-width: 768px) {

        #btn-submit-profit-loss {
            width: 51px;
            height: 34px;
            font-size: 10px;
        }

        .sampai-dengan {
            display: none !important;
        }

        .sampai-dengan-mobile {
            display: block !important;
            margin-top: 7px;
            margin-left: 4px;
            margin-right: 4px;
        }

        .select-month {
            width: 60px !important;
            margin-right: 10px;
        }

        .select-year {
            width: 60px !important;
        }

        .select-month2 {
            width: 60px !important;
            margin-right: 10px;
        }

        .select-year2 {
            width: 60px !important;
            margin-right: 7px;
        }

        #year_from {
            width: 131px;
        }

        #month_from {
            width: 100%;
        }



        .btn-insoft {}



        .tdate {
            width: 45%;
        }

        .tname {
            position: absolute;
            width: 50%;
            right: 13px;
        }

        .row-mobile {}

        .takun {
            width: 35%;
            margin-top: -40px;
        }

        .tdebit {
            position: relative;
            width: 27%;
            top: 7px;
            margin-left: 0%;
        }

        .tkredit {
            position: relative;
            top: -30px;
            width: 27%;
            margin-left: 28%;
        }

        .tkicknote {
            position: relative;
            top: -66px;
            width: 31%;
            margin-left: 57%;
        }

        .tdelete {
            position: relative;
            top: -99px;
            width: 1% !important;
            margin-left: 6%;
            height: 31px;
            right: -84%;
            border-radius: 25px;
        }

        .row-atas {
            margin-top: -34px !important;
        }

        .mobile-title {
            position: relative;
            margin-top: -58px;
            font-size: 12px !important;
            margin-left: -16px;
        }

        .del-item {}

        .row-item {
            margin-top: -61px;
        }

        .l-estimasi {
            display: none;
        }

        .l-debit {
            position: relative;
            top: 0px;
            margin-top: 9px;

        }

        .l-kredit {
            position: relative;
            top: -36px;
            left: 97px;
            margin-top: 14px;
        }

        .l-kicknote {
            display: none;
        }

        .label-total {
            position: relative;
            left: -85%;
        }

        .label-debit {
            position: relative;
            top: -21px;
            right: -51%;
        }

        .label-kredit {
            position: relative;
            top: -21px;
            right: -51%;
        }

        #bulan {}

        #tahun {
            margin-top: 7px;
        }


    }
</style>
