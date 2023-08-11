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
                        <div class="d-flex flex-column flex-md-row mb-3 gap-0 column-gap-3 transaction-filters">
                            <div class="text-center mb-3 mb-md-0">
                                <label for="filter_date_start"><b>{{__('Start Date')}}</b></label>
                                <input class="form-control text-center mx-auto" style="width:120px;" id="filter_date_start"/>
                            </div>
                            <div class="text-center mb-3 mb-md-0">
                                <label for="filter_date_end"><b>{{__('End Date')}}</b></label>
                                <input class="form-control text-center mx-auto" style="width:120px;" id="filter_date_end"/>
                            </div>
                            <div class="flex-fill">
                            </div>
                            <div class="text-center align-self-md-end">
                                <label for="filter_product"><b>{{__('Product')}}</b></label>
                                <select type="text" class="form-control mx-auto" style="width:120px;" id="filter_product">
                                    <option value="0">{{ __('All Products') }}</option>
                                    @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="transactions-list-notifications">
                            {{-- Loaded with AJAX --}}
                        </div>
                        <div class="transactions-list">
                            {{-- Loaded with AJAX --}}
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <canvas id="investmentChart" width="600" height="400"></canvas>
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
            let data = {
                page: nTransactionsCurrentPage,
                filter_date_start: $('#filter_date_start').val(),
                filter_date_end: $('#filter_date_end').val(),
                filter_product: $('#filter_product').val()
            };
            $.ajax({
                type: "GET",
                url: "{{ route('transaction.list') }}",
                data: data,
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
                let nId = btn.closest('.transaction').attr('data-id');
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
            let transaction = btn.closest('.transaction');
            let details = transaction.find('.transaction-details-container');
            let visible = details.is(':visible');
            $('.transaction-details-container').hide();
            if(!visible){
                details.show();
            }
        });
        // End Of Show Details Transaction Modal

        // Save transaction
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
                    console.log(response);
                    if(response.success === 1){
                        loadTransactionList();
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
            let nId = btn.closest('.transaction').attr('data-id');
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
                        loadTransactionList();
                        showNotification('.transactions-list-notifications', response.messages);
                        modal.hide();
                    }
                }
            });
        });
        // End Of Delete Transaction

        // Filters
        var startDatePicker;
        var endDatePicker;
        function createDatePicker(pickerSelector, minDate = null, maxDate = null){
            let instanceSelector = (pickerSelector === '#filter_date_start') ? 'startDatePicker' : 'endDatePicker';
            let currentDate = new Date();
            if(window[instanceSelector] !== undefined){
                currentDate = window[instanceSelector].getDate();
            }
            if(pickerSelector === '#filter_date_start'){
                maxDate = (maxDate === null) ? currentDate : maxDate;
            }else{
                minDate = (minDate === null) ? currentDate : minDate;
            }
            let currentPicker = new easepick.create({
                element: $(pickerSelector)[0],
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                ],
                plugins: [LockPlugin],
                LockPlugin: {
                    minDate: minDate,
                    maxDate: maxDate
                },
                zIndex: 4,
                date: currentDate,
                setup(picker) {
                    picker.on('select', function(e){
                        if(pickerSelector === '#filter_date_start'){
                            createDatePicker('#filter_date_end', e.detail.date);
                        }else{
                            createDatePicker('#filter_date_start', null, e.detail.date);
                        }
                        loadTransactionList();
                    })
                }
            });
            if(window[instanceSelector] !== undefined){
                window[instanceSelector].destroy();
            }
            window[instanceSelector] = currentPicker;
        }
        createDatePicker('#filter_date_start');
        createDatePicker('#filter_date_end');

        $(document).on('change', '#filter_product', function(){
            loadTransactionList();
        });
        // Chart
        var investmentChartCtx = document.getElementById('investmentChart').getContext('2d');

        // Example data, replace with your actual data
        var totalActivitiesAmount = 17000;
        var totalProductTransactionsAmount = -2000;

        var investmentChartData = {
            labels: ["Activities", "Product Transactions"],
            datasets: [
                {
                    label: "Amount (in $)",
                    //data vine din php
                    data: [totalActivitiesAmount, totalProductTransactionsAmount]
                }
            ]
        };

        var backgroundColors = investmentChartData.datasets[0].data.map(amount => amount >= 0 ? "#3cba9f" : "#ff0000");
        investmentChartData.datasets[0].backgroundColor = backgroundColors;

        var investmentChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        var investmentChart = new Chart(investmentChartCtx, {
            type: 'line',
            data: investmentChartData,
            options: investmentChartOptions
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
        .transacted-item-details-type-activity .transacted-item-details-product {
            display:none;
        }
        .transacted-item-details-type-product .transacted-item-details-activity {
            display:none;
        }
        .transacted-item-details-amount-positive .transacted-item-details-quantity , .transacted-item-details-amount-negative .transacted-item-details-amount strong{
            color:red;
        }
        .transacted-item-details-amount-negative .transacted-item-details-quantity , .transacted-item-details-amount-positive .transacted-item-details-amount strong{
            color:green;
        }

        .toc-list .toc-row {
            text-decoration: none;
            display: grid;
            grid-template-columns: auto max-content;
            align-items: end;
        }

        .toc-list .title {
            position: relative;
            overflow: hidden;
        }

        .toc-list .leaders::after {
            position: absolute;
            padding-inline-start: .25ch;
            content: " . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . "
            ". . . . . . . . . . . . . . . . . . . . . . . ";
            text-align: right;
        }

        .toc-list .page {
            min-width: 2ch;
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .transaction > td > div{
            border-radius: 10px;
            border: 1px solid #DEECDC;
        }
        .details-visible {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom: 0 !important;
        }

        .transaction-cell-date {
            max-width:154px;
        }
        .transaction-cell-actions{
           width:140px;
        }
        .easepick-wrapper #shadow-root .container{
            z-index:4;
        }
    </style>
@endsection

