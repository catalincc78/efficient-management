@php
    $isAmountPositive = ($item->amount > 0 || ($item->amount === 0 && $item->quantity <= 0));
@endphp

<div class="toc-list
    transacted-item-details-row{{$item->id === 0 ? ' placeholder-item' : ''}}{{$item->target_type === 'activity' ? ' transacted-item-details-type-activity' : ' transacted-item-details-type-product'}}
    {{$isAmountPositive ? ' transacted-item-details-amount-positive' : ' transacted-item-details-amount-negative'}}">
    <div class="toc-row px-3 py-2">
        @if($item->target_type === 'activity')
            <span class="title transacted-item-details-activity ps-1">
                 <strong>{{$item->activity}}</strong><span class="leaders" aria-hidden="true"></span>
            </span>
        @else
            <span class="title transacted-item-details-product ps-1">
                <strong>{{$item->product->name}}</strong> x <span class="transacted-item-details-quantity">{{$item->quantity}}</span><span class="leaders" aria-hidden="true"></span>
            </span>
        @endif
            <span class="page transacted-item-details-amount"><strong>{{$item->amount}}</strong></span>
    </div>
</div>
