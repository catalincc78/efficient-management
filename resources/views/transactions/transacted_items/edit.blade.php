@php
$item = $item ?? (object)[
  'id' => 0,
  'transaction_id' => 0,
  'target_type' => 'activity',
  'product_id' => null,
  'quantity' => 0,
  'activity' => null,
  'amount' => 0
];
$isAmountPositive = ($item->amount > 0 || ($item->amount === 0 && $item->quantity <= 0));
@endphp

<div class="row g-0 border border-dark border-opacity-25 rounded p-1 mb-2
    transacted-item-row{{$item->id === 0 ? ' placeholder-item' : ''}}{{$item->target_type === 'activity' ? ' transacted-item-type-activity' : ' transacted-item-type-product'}}
    {{$isAmountPositive ? ' transacted-item-amount-positive' : ' transacted-item-amount-negative'}}">
    <input type="hidden" name="id[]" value="{{$item->id}}">
    <input type="hidden" name="target_type[]" value="{{$item->target_type}}">
    <input type="hidden" name="is_amount_positive[]" value="{{$isAmountPositive}}">
    <div class="col col-12 col-lg-3 mb-2 mb-lg-0 pe-lg-2 d-flex ">
        <button style="height:37px;" type="button" class="btn btn-primary btn-transacted-item-toggle-amount-sign me-1">
            <div class="d-flex">
                <i class="fa-solid fa-arrow-up-long"></i>
                <i class="fa-sharp fa-solid fa-dollar-sign"></i>
            </div>
        </button>
        <div class="flex-fill transacted-item-amount">
            <input type="text" class="form-control input-type-float" name="amount[]" {!! $item->amount ?? ' value="'.$item->amount.'"' !!} placeholder="{{ __('Amount') }}">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <button style="height:37px;" type="button" class="btn btn-danger btn-transacted-item-delete d-lg-none ms-1"><i class="fa-solid fa-trash-can"></i></button>
    </div>

    <div class="col col-12 col-lg-9 d-flex">
        <button style="height:37px; width:40px;" type="button" class="btn btn-primary btn-transacted-item-toggle-type me-1"><i class="fa-solid fa-boxes-stacked"></i></button>
        <div class="flex-fill transacted-item-activity">
            <textarea rows="1" type="text" class="form-control" name="activity[]" placeholder="{{ __('Activity') }}">{{ $item->activity}}</textarea>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="flex-fill transacted-item-product me-1">
            <select type="text" class="form-control" name="product_id[]">
                <option value="0">{{ __('Choose Product') }}</option>
                @foreach($products as $product)
                <option value="{{$product->id}}"{{$item->product_id === $product->id ? ' selected' : ''}}>{{$product->name}}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="flex-fill transacted-item-quantity" style="max-width:130px;">
            <input type="text" class="form-control input-type-int" name="quantity[]"{!! $item->quantity ?? ' value="'.$item->quantity.'"' !!}" placeholder="{{ __('Quantity') }}">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <button style="height:37px;" type="button" class="btn btn-danger btn-transacted-item-delete d-none d-lg-block ms-1"><i class="fa-solid fa-trash-can"></i></button>
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
