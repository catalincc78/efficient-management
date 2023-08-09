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
        var openedTransactionDetails;
        
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

        // Show Add or Edit Transaction modal
        $(document).on('click', '.btn-transaction-add, .btn-transaction-edit', function(evt){
            let modalSelector = $('#modal-transaction-add-or-edit');
            let modal = bootstrap.Modal.getOrCreateInstance(modalSelector);
            let btn = $(evt.currentTarget);

            modalSelector.find('.form-control').removeClass('is-invalid'); // removing previous errors before loading modal
            modalSelector.find('form')[0].reset();
            modalSelector.find('.mtae-transacted-items-container').html('');
            if(btn.hasClass('btn-transaction-edit')){
                let nId = btn.closest('tr').attr('data-id');
                $.ajax({
                    type: "GET",
                    url: "transaction/" + nId,
                    dataType: "json",
                    success: function(response) {
                        if(response.success === 1){
                            let itemsContainer = modalSelector.find('.mtae-transacted-items-container');
                            itemsContainer.html(response.html);
                            modalSelector.find('[name="id"]').val(response.transaction.id);
                            modal.show();
                        }
                    }
                });
            }else{
                modalSelector.find('[name="id"]').val('');
                modal.show();
            }
        });
        // End of Show Add or Edit Transaction modal

        // Manage Items Row
        $(document).on('click', '#modal-transaction-add-or-edit .btn-add-item', function() {
            let modalSelector = $('#modal-transaction-add-or-edit');
            let itemsContainer = modalSelector.find('.mtae-transacted-items-container');
            let itemPlaceholder = modalSelector.find('.placeholder-item');
            let itemPlaceholderClone = itemPlaceholder.clone();
            itemPlaceholderClone.removeClass('placeholder-item');
            itemsContainer.append(itemPlaceholderClone);
        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-transacted-item-toggle-type', function(evt) {
            let item = $(evt.currentTarget).closest('.transacted-item-row');
            item.toggleClass('transacted-item-type-activity').toggleClass('transacted-item-type-product');
            item.find('[name="target_type[]"]').val(item.hasClass('transacted-item-type-activity' ) ? 'activity' : 'product') ;
        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-transacted-item-toggle-amount-sign', function(evt) {
            let item = $(evt.currentTarget).closest('.transacted-item-row');
            item.toggleClass('transacted-item-amount-positive').toggleClass('transacted-item-amount-negative');
            item.find('[name="is_amount_positive[]"]').val(item.hasClass('transacted-item-amount-positive' ) ? 1 : 0) ;
        });

        $(document).on('click', '#modal-transaction-add-or-edit .btn-transacted-item-delete', function(evt) {
            let item = $(evt.currentTarget).closest('.transacted-item-row');
            item.remove();
        });
        // End of Manage Items Row

        // Show Details Transaction Modal
        $(document).on('click', '.btn-transaction-details', function(evt){
            let btn = $(evt.currentTarget);
            let transaction = btn.closest('.transaction').next();
            let visible = transaction.is(':visible');
            $('.transaction-details-container').hide();
            if(!visible){
                transaction.show();
            }
        });
        // End Of Show Details Transaction Modal

        // Save transaction
        $(document).on('click', '#modal-transaction-add-or-edit .btn-save', function(){
            let data = $('#modal-transaction-add-or-edit form').serialize();
            data += '&page=' + nTransactionsCurrentPage;
            let nId = $('#modal-transaction-add-or-edit form [name="id"]').val();
            console.log('before ajax');
            $.ajax({
                type: nId ? "PUT" : "POST",
                url: nId ? "transaction/" + nId : "{{ route('transaction.add') }}",
                data: data,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if(response.success === 1){
                        loadTransactionList(response.html);
                        showNotification('.transactions-list-notifications', response.messages);
                        $('#modal-transaction-add-or-edit .btn-close').click();
                    }else{
                        if(response.messages.general !== undefined) {
                            showNotification('.transaction-add-or-edit-notification', [response.messages.general], 'danger');
                        }
                        $('#modal-transaction-add-or-edit .form-control').removeClass('is-invalid');
                        for(let fieldName in response.messages){
                            let arName = fieldName.split('.');
                            let field = $('#modal-transaction-add-or-edit [name="'+ arName[0] + '[]"]')[arName[1]];
                            $(field).addClass('is-invalid').closest('div').find('.invalid-feedback').html(response.messages[fieldName]);
                        }
                    }
                }
            });
        })

        // Delete Transaction
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
        // End Of Delete Transaction


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
