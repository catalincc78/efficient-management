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

@section('scripts')
    @parent
    <script type="module">
        $(document).ready(function() {
            $('#product-add-or-edit .btn-primary').on('click', function(){
                let data = $('#product-add-or-edit form').serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('product.add') }}",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        if(response.success === 1){
                            console.log('safa');
                            loadProductList(response.html);
                            console.log('safa2');
                            showNotification('.products-list-notifications', response.messages);
                            $('#product-add-or-edit .btn-close').click();
                            console.log('safa3');
                        }else{
                            console.log(response.messages);
                            $('#product-add-or-edit .form-control').removeClass('is-invalid');
                            for(let fieldName in response.messages){
                                $('#product-add-or-edit input[name="'+ fieldName + '"]').closest('div').find('.invalid-feedback').html(response.messages[fieldName]);
                                $('#product-add-or-edit .form-control').addClass('is-invalid');
                            }
                        }

                    }
                });
            })
        });
    </script>
@endsection
