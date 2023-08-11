@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Statistics') }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row mb-3 gap-0 column-gap-3 statistic-filters">
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
                    <div class="statistics-list-notifications">
                        {{-- Loaded with AJAX --}}
                    </div>
                    <div class="statistics-list">
                        {{-- Loaded with AJAX --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script type="module">
            var updateBarChart = function (labels, data) {
                var ctx = document.getElementById('amountChart').getContext('2d');

                // Define colors for positive and negative amounts
                var colors = data.map(amount => amount >= 0 ? 'green' : 'red');

                // Create the chart
                var barChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Amount',
                            data: data,
                            backgroundColor: colors
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            };
        document.addEventListener("DOMContentLoaded", function() {
            // Load and update the chart
            var loadStatisticsChart = function () {
                let data = {
                    filter_date_start: $('#filter_date_start').val(),
                    filter_date_end: $('#filter_date_end').val(),
                    filter_product: $('#filter_product').val()
                };
                $.ajax({
                    type: "GET",
                    url: "{{ route('statistic.list') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.success === 1) {
                            $('.statistics-list').html(response.html);
                            var chartData = response.chartData;
                            var labels = chartData.map(entry => entry.date);
                            var data = chartData.map(entry => entry.total_amount); // Use total_amount instead of amount
                            updateBarChart(labels, data);
                        }
                    }
                });
            }

            var startDatePicker;
            var endDatePicker;

            function createDatePicker(pickerSelector, minDate = null, maxDate = null) {
                let instanceSelector = (pickerSelector === '#filter_date_start') ? 'startDatePicker' : 'endDatePicker';
                let currentDate = new Date();
                if (window[instanceSelector] !== undefined) {
                    currentDate = window[instanceSelector].getDate();
                }
                if (pickerSelector === '#filter_date_start') {
                    maxDate = (maxDate === null) ? currentDate : maxDate;
                } else {
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
                        picker.on('select', function (e) {
                            if (pickerSelector === '#filter_date_start') {
                                createDatePicker('#filter_date_end', e.detail.date);
                            } else {
                                createDatePicker('#filter_date_start', null, e.detail.date);
                            }
                            loadStatisticsChart();
                        })
                    }
                });
                if (window[instanceSelector] !== undefined) {
                    window[instanceSelector].destroy();
                }
                window[instanceSelector] = currentPicker;
            }

            createDatePicker('#filter_date_start');
            createDatePicker('#filter_date_end');

            $(document).on('change', '#filter_product', function () {
                loadStatisticsChart();
            });
            loadStatisticsChart();
        });
    </script>
@endsection
@section('styles')
    @parent
    <style>
    </style>
@endsection
