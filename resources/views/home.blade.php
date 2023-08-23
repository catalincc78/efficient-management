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
                        <div class="row my-2">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="{{ route('product.main') }}" class="card-link card-link-no-underline">
                                            <div class="card mb-4">
                                                <div class="card-body bg-danger text-white text-center">
                                                    <h5 class="card-title font-weight-bold">{{__('Add Product')}}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-12">
                                        <a href="{{ route('transaction.main') }}" class="card-link card-link-no-underline">
                                            <div class="card mb-4">
                                                <div class="card-body bg-warning text-white text-center">
                                                    <h5 class="card-title font-weight-bold">{{__('See Transactions')}}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-12">
                                        <a href="{{ route('statistic.main') }}" class="card-link card-link-no-underline">
                                            <div class="card mb-4">
                                                <div class="card-body bg-primary text-white text-center">
                                                    <h5 class="card-title font-weight-bold">{{__('Statistic Charts')}}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4 bg-info text-white">
                                    <div class="card-body text-right">
                                        <h5 class="card-title">{{__('General Info')}}</h5>
                                        <p>{{__('Total number of products: ')}}: {{$products->count()}}</p>
                                        <p>{{__('Total number of transactions: ')}}: {{$transactions->count()}}</p>
                                    </div>
                                </div>
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
        .my-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
    </style>
@endsection
