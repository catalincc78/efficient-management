@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="row justify-content-between">
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('product.main') }}" class="btn btn-outline-primary btn-main-link">
                                    {{ __('Products') }}<br>{{ __('Management') }}
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a href="{{ route('transaction.main') }}" class="btn btn-outline-primary btn-main-link">
                                    {{ __('Transactions') }}<br>{{ __('Management') }}
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a href="{{ route('statistic.main') }}" class="btn btn-outline-primary btn-main-link">
                                    {{ __('Statistics') }}
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-main-link">
                                    {{ __('Edit') }}<br>{{ __('Profile') }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @parent
    <style>
        .btn-main-link {
            height: 200px;
            width: 100%;
            font-size: 24px !important;
            line-height: 1.2 !important;
            text-transform: uppercase;
            display: flex !important;
            justify-content: center;
            align-items: center;
        }
    </style>
@endsection
