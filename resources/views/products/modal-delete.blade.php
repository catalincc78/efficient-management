@section('modals')
@parent
<div class="modal fade" id="modal-product-delete" tabindex="-1" aria-labelledby="product-delete-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="product-delete-title">{{ __('Delete Product') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    @csrf
                    <input type="hidden" name="id">
                    <div class="text-center">{{ __('Are you sure you want to delete this product?') }}</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger">{{ __('Delete') }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection
