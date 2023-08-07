@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Transactions') }}</span>
                        <button type="button" class="btn btn-primary btn-transaction-add">{{ __('Add Transaction') }}</button>
                    </div>

                    <div class="card-body">
                        <div class="transactions-list-notifications">
                            {{-- Loaded with AJAX --}}
                        </div>
                        <div class="transactions-list">
                            {{-- Loaded with AJAX --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('transactions.modal-add-or-edit')
@include('transactions.modal-delete')
@section('scripts')
    @parent
    <script type="module">

        var nTransactionsCurrentPage = 1;
        var loadTransactionList = function(html = undefined) {
            if(html !== undefined){
                $('.transactions-list').html(html);
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('transaction.list') }}?page=" + nTransactionsCurrentPage,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        $('.transactions-list').html(response.html);
                        nTransactionsCurrentPage = response.current_page;
                    }
                }
            });
        }

        $(document).on('click', '.transactions-list .page-item a.page-link', function(evt) {
            evt.preventDefault();

            let url = new URL($(evt.target).attr('href'));
            let page = url.searchParams.get('page') ?? 1;
            if(page !== nTransactionsCurrentPage){
                nTransactionsCurrentPage = page;
                loadTransactionList();
            }
        });

        // Add Transaction
        $(document).on('click', '.btn-transaction-add, .btn-transaction-edit', function(evt){
            let modalSelector = $('#modal-transaction-add-or-edit');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let btn = $(evt.currentTarget);

            modalSelector.find('.form-control').removeClass('is-invalid'); // removing previous errors before loading modal
            modalSelector.find('form')[0].reset();
            if(btn.hasClass('btn-transaction-edit')){
                let nId = btn.closest('tr').attr('data-id');
                $.ajax({
                    type: "GET",
                    url: "transaction/" + nId,
                    dataType: "json",
                    success: function(response) {
                        if(response.success === 1){
                            modalSelector.find('[name="id"]').val(response.transaction.id);
                            modalSelector.find('[name="name"]').val(response.transaction.name);
                            modalSelector.find('[name="sku"]').val(response.transaction.sku);
                            modal.show();
                        }
                    }
                });
            }else{
                modalSelector.find('[name="id"]').val('');
                modal.show();
            }
        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-add-item', function() {
            let modalSelector = $('#modal-transaction-add-or-edit');
            let itemsContainer = modalSelector.find('.mtae-transacted-items-container');
            let itemPlaceholder = modalSelector.find('.placeholder-item');
            let itemPlaceholderClone = itemPlaceholder.clone();
            itemPlaceholderClone.removeClass('placeholder-item');

            itemsContainer.append(itemPlaceholderClone);
        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-transacted-item-toggle-type', function(evt) {
            let modalSelector = $('#modal-transaction-add-or-edit');
            let item = $(evt.currentTarget).closest('.transacted-item-row');
            item.toggleClass('transacted-item-type-activity');
            item.toggleClass('transacted-item-type-product');

            let icon = item.hasClass('transacted-item-type-activity') ? 'fa-boxes-stacked' : 'fa-box';
            $(evt.currentTarget).find('i').removeClass().addClass('fa-solid ' + icon);

        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-save', function(){
            let data = $('#modal-transaction-add-or-edit form').serialize();
            data += '&page=' + nTransactionsCurrentPage;
            let nId = $('#modal-transaction-add-or-edit form [name="id"]').val();
            $.ajax({
                type: nId ? "PUT" : "POST",
                url: nId ? "transaction/" + nId : "{{ route('transaction.add') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        loadTransactionList(response.html);
                        showNotification('.transactions-list-notifications', response.messages);
                        $('#modal-transaction-add-or-edit .btn-close').click();
                    }else{
                        console.log(response.messages);
                        $('#modal-transaction-add-or-edit .form-control').removeClass('is-invalid');
                        for(let fieldName in response.messages){
                            $('#modal-transaction-add-or-edit input[name="'+ fieldName + '"]').closest('div').find('.invalid-feedback').html(response.messages[fieldName]);
                            $('#modal-transaction-add-or-edit .form-control').addClass('is-invalid');
                        }
                    }
                }
            });
        })

        $(document).on('click', '.btn-transaction-delete', function(evt){
            let modalSelector = $('#modal-transaction-delete');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let btn = $(evt.currentTarget);
            let nId = btn.closest('tr').attr('data-id');
            modalSelector.find('[name="id"]').val(nId);
            modal.show();
        });

        $(document).on('click', '#modal-transaction-delete .btn-danger', function(evt) {
            let modalSelector = $('#modal-transaction-delete');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let nId = modalSelector.find('[name="id"]').val();
            let data = modalSelector.find('form').serialize();
            data += '&page=' + nTransactionsCurrentPage;

            $.ajax({
                type: "DELETE",
                url: "transaction/" + nId,
                data: data,
                dataType: "json",
                success: function(response) {
                    if(response.success === 1){
                        loadTransactionList(response.html);
                        showNotification('.transactions-list-notifications', response.messages);
                        modal.hide();
                    }
                }
            });
        });



        $(document).ready(function() {
            loadTransactionList();
        });
    </script>
@endsection

@section('styles')
    @parent
    <style>
        .transactions-list .pagination{
            margin-bottom: 0;
        }
        .transactions-list nav p{
            margin-bottom: 0;
        }
    </style>
@endsection
