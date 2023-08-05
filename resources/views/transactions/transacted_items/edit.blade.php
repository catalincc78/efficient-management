@php
$item = $item ?? (object)[
  'id' => 0,
  'transaction_id' => 0,
  'product_id' => null,
  'quantity' => 0,
  'activity' => null,
  'amount' => 0
];
@endphp

<div class="d-flex flex-row transacted-item-row{{$item->id === 0 ? ' placeholder-item' : ''}}{{is_null($item->product_id) ? ' transacted-item-type-activity' : ' transacted-item-type-product'}}">
    <div class="flex-column">
        <button type="button" class="btn btn-primary btn-transacted-item-toggle-amount-sign"><i class="fa-solid fa-pencil"></i></button>
        <button type="button" class="btn btn-primary btn-transacted-item-toggle-type"><i class="fa-solid fa-pencil"></i></button>
    </div>
    <div class="flex-column">
        <div class="mb-3 transacted-item-amount">
            <label for="mpae-product-sku" class="col-form-label">{{ __('Amount') }}</label>
            <input type="text" class="form-control" name="sku" id="mpae-product-sku">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="mb-3 transacted-item-quantity">
            <label for="mpae-product-sku" class="col-form-label">{{ __('Quantity') }}</label>
            <input type="text" class="form-control" name="sku" id="mpae-product-sku">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
    </div>

    <div class="flex-column">
        <div class="mb-3 transacted-item-activity">
            <label for="mpae-product-sku" class="col-form-label">{{ __('Activity') }}</label>
            <textarea type="text" class="form-control" name="sku" id="mpae-product-sku"></textarea>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="mb-3 transacted-item-product">
            <label for="mpae-product-sku" class="col-form-label">{{ __('Product') }}</label>
            <select type="text" class="form-control" name="sku" id="mpae-product-sku">
                <option>Item 1</option>
                <option>Item 2</option>
            </select>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="mb-3 transacted-item-add-new-product">
            <button type="button" class="btn btn-primary btn-transaction-edit">{{ __('Add New Product') }}</button>
        </div>
    </div>

</div>
