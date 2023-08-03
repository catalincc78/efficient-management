@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Products') }}</span>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#product-add-or-edit">{{ __('Add') }}</button>
                    </div>

                    <div class="card-body">
                        <div class="products-list-notifications">
                            {{-- Loaded with AJAX --}}
                        </div>
                        <div class="products-list">
                            {{-- Loaded with AJAX --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('products.modal-add-or-edit')
@section('scripts')
    @parent
    <script type="module">
        var loadProductList = function(html = undefined) {
            if(html !== undefined){
                $('.products-list').html(html);
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('product.list') }}",
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        $('.products-list').html(response.html);
                    }
                }
            });
        }
        $(document).ready(function() {
            loadProductList();
        });
        window.loadProductList = loadProductList;
    </script>
@endsection
