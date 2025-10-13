@extends('dashboard.layouts.app')

@section('content')
    <div class="col-xl-12 col-md-12 proorder-md-1">
        <div class="row">
            <div class="col-xl-4 col-sm-6">
            <a  href="#">
                <div class="card">
                    <div class="card-body student">
                        <div class="d-flex gap-2 align-items-end">
                            <div class="flex-grow-1">
                                <h2></h2>
                                <p class="mb-0 text-truncate"> User </p>
                            </div>
                            <div class="flex-shrink-0">
                            <img src="{{ asset('dashboard/assets/images/user/profile1.png') }}" alt="">
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection
