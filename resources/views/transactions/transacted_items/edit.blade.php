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

<div class="row g-0 border border-dark border-opacity-25 rounded p-1 mb-2 transacted-item-row{{$item->id === 0 ? ' placeholder-item' : ''}}{{is_null($item->product_id) ? ' transacted-item-type-activity' : ' transacted-item-type-product'}}">
    <div class="col col-12 col-lg-3 mb-2 mb-lg-0 pe-lg-2 d-flex ">
        <button style="height:37px;" type="button" class="btn btn-primary btn-transacted-item-toggle-amount-sign me-1">
            <div class="d-flex">
                <i class="fa-solid fa-arrow-up"></i>
                <i class="fa-sharp fa-solid fa-dollar-sign fa-sm"></i>
            </div>
        </button>
        <div class="flex-fill transacted-item-amount">
            <input type="text" class="form-control" name="amount" id="mtae-transaction-amount" placeholder="{{ __('Amount') }}">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <button style="height:37px;" type="button" class="btn btn-danger btn-transacted-item-delete d-lg-none ms-1"><i class="fa-solid fa-trash-can"></i></button>
    </div>

    <div class="col col-12 col-lg-9 d-flex">
        <button style="height:37px;" type="button" class="btn btn-primary btn-transacted-item-toggle-type me-1"><i class="fa-solid fa-boxes-stacked"></i></button>
        <div class="flex-fill transacted-item-activity">
            <textarea rows="1" type="text" class="form-control" name="activity" id="mtae-transaction-activity" placeholder="{{ __('Activity') }}"></textarea>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="flex-fill transacted-item-product me-1">
            <select type="text" class="form-control" name="item" id="mpae-product-item">
                <option>Item 1</option>
                <option>Item 2</option>
            </select>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <div class="flex-fill transacted-item-quantity" style="max-width:130px;">
            <input type="text" class="form-control" name="quantity" id="mtae-transaction-quantity" placeholder="{{ __('Quantity') }}">
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>
        <button style="height:37px;" type="button" class="btn btn-danger btn-transacted-item-delete d-none d-lg-block ms-1"><i class="fa-solid fa-trash-can"></i></button>
    </div>
</div>
