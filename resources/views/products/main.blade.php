@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Products') }}</span>
                        <button type="button" class="btn btn-primary btn-product-add">{{ __('Add') }}</button>
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
@include('products.modal-delete')
@section('scripts')
    @parent
    <script type="module">
        // Variabile globale pentru gestionarea paginării produselor
        var nProductsCurrentPage = 1;
        // Funcția pentru încărcarea listei de produse
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
        // Eveniment pentru paginare - schimbarea paginii produselor
        $(document).on('click', '.products-list .page-item a.page-link', function(evt) {
            evt.preventDefault();

            let url = new URL($(evt.target).attr('href'));
            let page = url.searchParams.get('page') ?? 1;
            if(page !== nProductsCurrentPage){
                nProductsCurrentPage = page;
                loadProductList();
            }
        });

        // Eveniment pentru afișarea modalului de adăugare/editare produs
        $(document).on('click', '.btn-product-add, .btn-product-edit', function(evt){
            let modalSelector = $('#modal-product-add-or-edit');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let btn = $(evt.currentTarget);

            modalSelector.find('.form-control').removeClass('is-invalid'); // removing previous errors before loading modal
            modalSelector.find('form')[0].reset();
            if(btn.hasClass('btn-product-edit')){
                let nId = btn.closest('tr').attr('data-id');
                $.ajax({
                    type: "GET",
                    url: "product/" + nId,
                    dataType: "json",
                    success: function(response) {
                        if(response.success === 1){
                            modalSelector.find('[name="id"]').val(response.product.id);
                            modalSelector.find('[name="name"]').val(response.product.name);
                            modalSelector.find('[name="sku"]').val(response.product.sku);
                            modal.show();
                        }
                    }
                });
            }else{
                modalSelector.find('[name="id"]').val('');
                modal.show();
            }
        });

        // Eveniment pentru ștergerea unui produs
        $(document).on('click', '.btn-product-delete', function(evt){
            let modalSelector = $('#modal-product-delete');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let btn = $(evt.currentTarget);
            let nId = btn.closest('tr').attr('data-id');
            modalSelector.find('[name="id"]').val(nId);
            modal.show();
        });

        // Eveniment pentru confirmarea ștergerii unui produs
        $(document).on('click', '#modal-product-delete .btn-danger', function(evt) {
            let modalSelector = $('#modal-product-delete');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let nId = modalSelector.find('[name="id"]').val();
            let data = modalSelector.find('form').serialize();
            data += '&page=' + nProductsCurrentPage;

            $.ajax({
                type: "DELETE",
                url: "product/" + nId,
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        loadProductList(response.html);
                        showNotification('.products-list-notifications', response.messages);
                        modal.hide();
                    }
                }
            });
        });

        // Eveniment pentru salvarea produsului
        $(document).on('click', '#modal-product-add-or-edit .btn-primary', function(){
            let data = $('#modal-product-add-or-edit form').serialize();
            data += '&page=' + nProductsCurrentPage;
            let nId = $('#modal-product-add-or-edit form [name="id"]').val();
            $.ajax({
                type: nId ? "PUT" : "POST",
                url: nId ? "product/" + nId : "{{ route('product.add') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        loadProductList(response.html);
                        showNotification('.products-list-notifications', response.messages);
                        $('#modal-product-add-or-edit .btn-close').click();
                    }else{
                        $('#modal-product-add-or-edit .form-control').removeClass('is-invalid');
                        if(response.errors === 1) {
                            showNotification('.products-modal-notifications', response.messages, 'danger');
                        }else {
                            for (let fieldName in response.messages) {
                                $('#modal-product-add-or-edit input[name="' + fieldName + '"]').addClass('is-invalid');
                                $('#modal-product-add-or-edit input[name="' + fieldName + '"]').closest('div').find('.invalid-feedback').html(response.messages[fieldName]);
                            }
                        }
                    }
                }
            });
        })

        // Inițializarea încărcării listei de produse la încărcarea paginii
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
    </style>
@endsection
