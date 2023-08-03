@section('modals')
@parent
<div class="modal fade" id="product-add-or-edit" tabindex="-1" aria-labelledby="product-add-or-edit-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="product-add-or-edit-title">{{ __('Product Info') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label for="mpae-product-name" class="col-form-label">{{ __('Product Name') }}</label>
                        <input type="text" class="form-control" name="name" id="mpae-product-name">
                        <span class="invalid-feedback" role="alert"><strong></strong></span>
                    </div>
                    <div class="mb-3">
                        <label for="mpae-product-sku" class="col-form-label">{{ __('Product SKU') }}</label>
                        <input type="text" class="form-control" name="sku" id="mpae-product-sku">
                        <span class="invalid-feedback" role="alert"><strong></strong></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">{{ __('Save') }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection
