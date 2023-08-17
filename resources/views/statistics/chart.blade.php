<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ $chart['title'] }}</span>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row mb-3 gap-0 column-gap-3 statistic-filters">
                @if(in_array('date_range', $chart['filters']))
                    <div class="text-center mb-3 mb-md-0">
                        <label for="filter_date_start_{{$chart['id']}}"><b>{{__('Start Date')}}</b></label>
                        <input class="form-control text-center mx-auto" style="width:120px;" id="filter_date_start_{{$chart['id']}}"/>
                    </div>
                    <div class="text-center mb-3 mb-md-0">
                        <label for="filter_date_end_{{$chart['id']}}"><b>{{__('End Date')}}</b></label>
                        <input class="form-control text-center mx-auto" style="width:120px;" id="filter_date_end_{{$chart['id']}}"/>
                    </div>
                @endif
                <div class="flex-fill">
                </div>
                @if(in_array('products_all', $chart['filters']))
                    <div class="text-center align-self-md-end">
                        <label for="filter_product_{{$chart['id']}}"><b>{{__('Product')}}</b></label>
                        <select type="text" class="form-control mx-auto" style="width:120px;" id="filter_product_{{$chart['id']}}">
                            <option value="0">{{ __('All Products') }}</option>
                            @foreach($products as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            <div class="statistics-list">
                <div class="mt-3">
                    <canvas id="chart_{{$chart['id']}}" width="600" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
