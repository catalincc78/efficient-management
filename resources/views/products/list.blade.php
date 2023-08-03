<table class="table">
    <thead>
        <tr>
            <th scope="col">{{ __('Name') }}</th>
            <th scope="col" class="text-start">{{ __('SKU') }}</th>
            <th scope="col" class="text-end">{{ __('Stock') }}</th>
            <th scope="col" class="text-end"><div class="pe-3">{{ __('Actions') }}</div></th>
        </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td >{{$product->name}}</td>
            <td class="text-start">{{$product->sku}}</td>
            <td class="text-end">{{$product->stock}}</td>
            <td class="text-end">
                <button type="button" class="btn btn-primary"><i class="fa-solid fa-pencil"></i></button>
                <button type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i></button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $products->links()!!}
