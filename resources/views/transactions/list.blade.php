<table class="table">
    <thead>
    <tr>
        <th scope="col">{{ __('Date') }}</th>
        <th scope="col" class="text-end">{{ __('Amount') }}</th>
        <th scope="col" class="text-end"><div class="pe-3">{{ __('Actions') }}</div></th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr data-id="{{$transaction->id}}">
            <td >{{$transaction->name}}</td>
            <td class="text-end">{{$transaction->stock}}</td>
            <td class="text-end">
                <button type="button" class="btn btn-primary btn-transaction-edit"><i class="fa-solid fa-pencil"></i></button>
                <button type="button" class="btn btn-danger btn-transaction-delete"><i class="fa-regular fa-trash-can"></i></button>
                <button type="button" class="btn btn-info btn-transaction-details"><i class="bi bi-list-ol"></i></button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $transactions->onEachSide(1)->links() !!}
