@php
    $isAmountPositive = ($item->amount > 0 || ($item->amount === 0 && $item->quantity <= 0));
@endphp

<div class="row g-0 border border-dark border-opacity-25 rounded p-1 mb-2
    transacted-item-row{{$item->id === 0 ? ' placeholder-item' : ''}}{{$item->target_type === 'activity' ? ' transacted-item-type-activity' : ' transacted-item-type-product'}}
    {{$isAmountPositive ? ' transacted-item-amount-positive' : ' transacted-item-amount-negative'}}">
    <div class="col col-12 col-lg-9 d-flex">
        @if($item->target_type === 'activity')
            <div class="flex-fill transacted-item-activity">
                 <strong>{{$item->activity}}</strong>
            </div>
        @else
            <div class="flex-fill transacted-item-product me-1">
                <strong>{{$item->product->name}}</strong>
            </div>
            <div class="flex-fill me-1">
            </div>
            <div class="flex-fill transacted-item-quantity" style="max-width:130px;">
                <strong>x{{$item->quantity}}</strong>
            </div>
        @endif
        <div class="col col-12 col-lg-3 mb-2 mb-lg-0 pe-lg-2 d-flex ">
            <div class="flex-fill transacted-item-amount" style="{{$isAmountPositive ? 'color: green;' : 'color: red;'}}">
                <strong>{{$item->amount}}</strong>
            </div>
        </div>
    </div>
</div>


@section('styles')
    <style>
        .transacted-item-type-activity .transacted-item-product,
        .transacted-item-type-activity .transacted-item-quantity,
        .transacted-item-type-activity .transacted-item-add-new-product{
            display:none;
        }
        .transacted-item-type-product .transacted-item-activity {
            display:none;
        }

        .transacted-item-type-activity .btn-transacted-item-toggle-type i:before{
            content: "\f468";
        }
        .transacted-item-type-product .btn-transacted-item-toggle-type i:before{
            content: "\f466";
        }
        .transacted-item-amount-positive .btn-transacted-item-toggle-amount-sign i:nth-of-type(1)::before{
            content: "\f176";
            color:greenyellow;
        }
        .transacted-item-amount-negative .btn-transacted-item-toggle-amount-sign i:nth-of-type(1)::before{
            content: "\f175";
            color:red;
        }
        .transacted-item-amount-positive .transacted-item-quantity input, .transacted-item-amount-negative .transacted-item-amount input{
            color:red;
        }
        .transacted-item-amount-negative .transacted-item-quantity input, .transacted-item-amount-positive .transacted-item-amount input{
            color:green;
        }
    </style>
@endsection
