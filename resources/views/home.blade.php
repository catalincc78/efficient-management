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
                            <div class="col-md-4 mb-4">
                                <a href="{{ route('product.main') }}" class="card-link card-link-no-underline">
                                    <div class="card">
                                        <div class="card-body d-flex flex-column text-center" style="height: 100%;">
                                            <h5 class="card-title font-weight-bold">{{ __('Products') }}<br>{{ __('Management') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 mb-4 d-md-block">
                                <a href="{{ route('transaction.main') }}" class="card-link card-link-no-underline">
                                    <div class="card">
                                        <div class="card-body d-flex flex-column text-center" style="height: 100%;">
                                            <h5 class="card-title font-weight-bold">{{ __('Transactions') }}<br>{{ __('Management') }}</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 mb-4">
                                <a href="{{ route('statistic.main') }}" class="card-link card-link-no-underline">
                                    <div class="card" style="height: 100%;">
                                        <div class="card-body d-flex flex-column text-center" style="height: 100%;">
                                            <h5 class="card-title font-weight-bold">{{ __('Statistics') }}</h5>
                                        </div>
                                    </div>
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
        .card-link-no-underline {
            text-decoration: none !important;
            color: inherit;
        }
    </style>
@endsection
