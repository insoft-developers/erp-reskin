@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Notifikasi</h5>
                            </div>


                            <!-- Notification List View -->
                            <div class="list-group">
                                @foreach ($data as $item)
                                    <div class="row border-bottom p-3">
                                        <div class="col-md-1 mb-1">
                                            @if($item->image != null)
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="Notification Image"
                                                width="100px">
                                            @endif
                                        </div>
                                        <div class="col-md-11">
                                            <a href="{{ route('notification.show', $item->id) }}">
                                                <h5 class="mb-1">{{ $item->title }}</h5>
                                                <div class="mb-1">
                                                    {!! Str::limit($item->description, 250) !!}
                                                </div>
                                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <!-- [Recent Orders] end -->
                    <!-- [Table] start -->
                    <!-- [Table] end -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        
        </div>
@endsection
@section('js')
@endsection
