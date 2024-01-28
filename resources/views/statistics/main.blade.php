@extends('layouts.app')
@php
    $charts = [
        [
            'id' => 'daily_amount',
            'title' => 'Daily Amount',
            'filters' => ['date_range', 'products_all'],
            'graph_type' => 'bar',
            'url' => route('statistic.chart.daily-amount')
        ],
//        [
//            'id' => 'daily_stock',
//            'title' => 'Daily Stock',
//            'filters' => ['date_range', 'products'],
//            'graph_type' => 'bar',
//            'url' => route('statistics.chart.daily-stock')
//        ],
//        [
//            'id' => 'total_amount',
//            'title' => 'Total Amount',
//            'filters' => ['date_range', 'products_all'],
//            'graph_type' => 'line',
//            'url' => route('statistics.chart.total-amount')
//        ],
        [
            'id' => 'profit_per_product',
            'title' => 'Profit per Product',
            'filters' => ['date_range', 'products_all'],
            'graph_type' => 'pie',
            'url' => route('statistic.chart.profit-per-product')
        ],
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
        // Definirea variabilei 'arCharts' pentru stocarea datelor despre grafice
        let arCharts = {!! json_encode($charts) !!};

        // Funcție pentru actualizarea graficului cu datele furnizate
        let updateChart = function (oChart, chartData) {
            let selector = '#chart_' + oChart['id'];
            let ctx = $(selector)[0].getContext('2d');
            let datasets = [];
            let labels = [];
            // Construirea seturilor de date și etichetelor pentru grafic
            for(let index in chartData)
            {
                // if(index === '0'){
                //     labels = (chartData[index].map(entry => entry.label));
                // }
                chartData[index].map(entry =>  labels.push(entry.label));
                let colors = chartData[index].map(entry => entry.color ?? (entry.value >= 0 ? 'green' : 'red'));
                datasets.push({ data: chartData[index].map(entry => entry.value), backgroundColor: colors });
            }

            // Distrugerea graficului existent și crearea unuia nou
            let graph = $(selector).data('graph');
            if(graph) {
                graph.destroy();
            }
            graph =  new Chart(ctx, {
                type: oChart['graph_type'],
                data: datasets.length === 1 ? {
                    datasets: datasets,
                    labels: labels
                } : {
                    datasets: datasets
                },
                options: {
                    plugins: {
                        legend: false,
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const labelIndex = (context.datasetIndex * 2) + context.dataIndex;
                                    return (datasets.length > 1 ? labels[labelIndex] + ': ' : '') + context.formattedValue;
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
            $(selector).data('graph', graph);
        };

        // Funcție pentru încărcarea datelor pentru un grafic specific
        let loadStatisticsChart = function (oChart) {
            let chartId = oChart['id'];
            let data = {
                filter_date_start: $('#filter_date_start_' + chartId).val(),
                filter_date_end: $('#filter_date_end_' + chartId).val(),
                filter_product: $('#filter_product_' + chartId).val()
            };
            // Apelarea unei solicitări AJAX pentru obținerea datelor
            $.ajax({
                type: "GET",
                url: oChart['url'],
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response.success === 1) {
                        $('#statistics_list_' + chartId).html(response.html);
                        updateChart(oChart, response.chartData);
                    }
                }
            });
        }

        // Iterarea prin grafice și inițierea funcționalității pentru fiecare
        for(let i = 0; i < arCharts.length; i++){
            let oChart = arCharts[i];
            createDatePicker('#filter_date_start_' + oChart['id'], function(){loadStatisticsChart(oChart)});
            createDatePicker('#filter_date_end_' + oChart['id'], function(){loadStatisticsChart(oChart)});

            $(document).on('change', '#filter_product_' + oChart['id'], function () {
                loadStatisticsChart(oChart);
            });
            loadStatisticsChart(oChart);
        }

    </script>
@endsection
@section('styles')
    @parent
    <style>
    </style>
@endsection
