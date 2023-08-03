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
        var nProductsCurrentPage = 1;
        var loadProductList = function(html = undefined) {
            if(html !== undefined){
                $('.products-list').html(html);
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('product.list') }}?page=" + nProductsCurrentPage,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        $('.products-list').html(response.html);
                        nProductsCurrentPage = response.current_page;
                    }
                }
            });
        }

        $(document).on('click', '.products-list .page-item a.page-link', function(evt) {
            evt.preventDefault();

            let url = new URL($(evt.target).attr('href'));
            let page = url.searchParams.get('page') ?? 1;
            if(page !== nProductsCurrentPage){
                nProductsCurrentPage = page;
                loadProductList();
            }
        });

        $(document).on('click', '#product-add-or-edit .btn-primary', function(){
            let data = $('#product-add-or-edit form').serialize();

            console.log('afsfasg');
            $.ajax({
                type: "POST",
                url: "{{ route('product.add') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        console.log('safa');
                        loadProductList(response.html);
                        console.log('safa2');
                        showNotification('.products-list-notifications', response.messages);
                        $('#product-add-or-edit .btn-close').click();
                        console.log('safa3');
                    }else{
                        console.log(response.messages);
                        $('#product-add-or-edit .form-control').removeClass('is-invalid');
                        for(let fieldName in response.messages){
                            $('#product-add-or-edit input[name="'+ fieldName + '"]').closest('div').find('.invalid-feedback').html(response.messages[fieldName]);
                            $('#product-add-or-edit .form-control').addClass('is-invalid');
                        }
                    }
                }
            });
        })



        $(document).ready(function() {
            loadProductList();
        });
    </script>
@endsection

@section('styles')
    @parent
    <style>
        .products-list .pagination{
            margin-bottom: 0;
        }
        .products-list nav p{
            margin-bottom: 0;
        }
        @media screen and ( max-width: 1024px ){

            li.page-item {

                display: none;
            }

            .page-item:first-child,
            .page-item:nth-child( 2 ),
            .page-item:nth-child( 3 ),
            .page-item:nth-last-child( 2 ),
            .page-item:nth-last-child( 3 ),
            .page-item:last-child,
            .page-item.active,
            .page-item.disabled {

                display: block;
            }
        }
    </style>
@endsection
