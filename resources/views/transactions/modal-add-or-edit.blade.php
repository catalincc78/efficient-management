@section('modals')
    @parent
    <div class="modal fade modal-xl" id="modal-transaction-add-or-edit" tabindex="-1" aria-labelledby="transaction-add-or-edit-title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="transaction-add-or-edit-title">{{ __('Transaction Info') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form>
                        @csrf
                        <input type="hidden" name="id">
                        <div class="mb-3 mtae-transacted-items-container">
                            {{-- Populated by AJAX --}}
                        </div>
                    </form>
                    <div class="d-none placeholder-container">
                        @include('transactions.transacted_items.edit')

                    </div>
                    <button type="button" class="btn btn-info btn-add-item">{{ __('Add Item') }}</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-save">{{ __('Save') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

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
    </style>
@endsection
