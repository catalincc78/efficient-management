@extends('layouts.app')
@php
    $charts = [
        [
            'id' => 'daily_amount',
            'title' => 'Daily Amount',
            'filters' => ['date_range', 'products_all']
        ],
        [
            'id' => 'daily_stock',
            'title' => 'Daily Stock',
            'filters' => ['date_range', 'products']
        ]
    ];
@endphp
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach($charts as $chart)
            @include('statistics.chart')
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script type="module">
        let arCharts = {!! json_encode($charts) !!};
        let updateChart = function (selector, labels, data) {
            let ctx = $(selector)[0].getContext('2d');
            let colors = data.map(amount => amount >= 0 ? 'green' : 'red');
            let graph = $(selector).data('graph');
            let datasets = [{
                data: data,
                backgroundColor: colors
            }];
            if(graph) {
                graph.data.labels = labels;
                graph.data.datasets = datasets;
                graph.update();
            }else {
                graph =  new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        plugins: {
                            legend: false
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
                $(selector).data('graph', graph);
            }
        };
        let loadStatisticsChart = function (chartId) {
            let data = {
                filter_date_start: $('#filter_date_start_' + chartId).val(),
                filter_date_end: $('#filter_date_end_' + chartId).val(),
                filter_product: $('#filter_product_' + chartId).val()
            };
            $.ajax({
                type: "GET",
                url: "{{ route('statistic.list') }}",
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response.success === 1) {
                        $('#statistics_list_' + chartId).html(response.html);
                        var chartData = response.chartData;
                        var labels = chartData.map(entry => entry.date);
                        var data = chartData.map(entry => entry.total_amount);
                        updateChart('#chart_' + chartId,labels, data);
                    }
                }
            });
        }
        for(let i = 0; i < arCharts.length; i++){
            let oChart = arCharts[i];
            createDatePicker('#filter_date_start_' + oChart['id'], function(){loadStatisticsChart(oChart['id'])});
            createDatePicker('#filter_date_end_' + oChart['id'], function(){loadStatisticsChart(oChart['id'])});

            $(document).on('change', '#filter_product_' + oChart['id'], function () {
                loadStatisticsChart(oChart['id']);
            });
            loadStatisticsChart(oChart['id']);
        }

    </script>
@endsection
@section('styles')
    @parent
    <style>
    </style>
@endsection
