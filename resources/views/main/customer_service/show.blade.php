@extends('master', [
    'use_tailwind' => true,
])
{{-- @dd($products) --}}
@section('style')
    <style>
        .modal-backdrop {
            display: none;
        }

        .modal {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
@endsection
@section('content')
    <div id="app">
        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Customer Service</li>
                            <li class="breadcrumb-item">Layanan Customer Service</li>
                            <li class="breadcrumb-item">Detail Of Templates</li>
                        </ul>
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="page-header-right-items">
                            {{--  --}}
                        </div>
                    </div>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-xxl-12">
                            <form method="post" action="{{ route('customer-service.show.saveTemplate', ['id' => $id]) }}"
                                class="card stretch stretch-full">
                                @csrf
                                <div class="card-header">
                                    <h5 class="card-title">Detail Of Templates</h5>
                                    <div class="flex flex-row">
                                        {{--  --}}
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <input type="hidden" name="cs_id" value="{{ $id }}">
                                    @foreach ($form as $item)
                                        <div class="mb-3">
                                            <label for="{{ $item['key'] }}" class="form-label">{{ $item['title'] }}</label>
                                            <textarea required class="form-control @error($item['key']) is-invalid @enderror" name="template_{{ $item['key'] }}"
                                                id="{{ $item['key'] }}" rows="3">{{ $item['value'] }}</textarea>
                                            <div id="passwordHelpBlock" class="form-text">
                                                {{ $item['info'] }}
                                            </div>
                                            @error($item['key'])
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                                <div class="card-footer flex justify-end gap-[4px]">
                                    <a href="{{ url('/customer-service') }}" class="btn btn-danger">Back</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> --}}
    {{-- <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.js"></script> --}}
    <script>
        // const {
        //     createApp,
        //     ref,
        //     reactive,
        //     onMounted
        // } = Vue;
        const cs_id = {{ $id }}

        let table = null
        createApp({
            setup() {
                const data = reactive({
                    masterTemplateTemps: []
                })

                const methods = {
                    //
                }

                onMounted(() => {
                    //
                })

                return {
                    moment,
                    data,
                    methods
                }
            }
        }).mount('#app');
    </script>
@endsection
