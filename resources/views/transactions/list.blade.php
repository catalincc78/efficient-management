<table class="table">
    <thead>
    <tr>
        <th scope="col">{{ __('Date') }}</th>
        <th scope="col" class="text-end">{{ __('Amount') }}</th>
        <th scope="col" class="text-end"><div style="padding-right: 2.5rem !important;">{{ __('Actions') }}</div></th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr class="transaction" data-id="{{$transaction->id}}">
            <td class="pt-3" >{{$transaction->created_at}}</td>
            <td class="pt-3 text-end">{{number_format($transaction->total, 2)}}</td>
            <td class="text-end">
                <button type="button" class="btn btn-info btn-transaction-details"><i class="fa-solid fa-list-ol"></i></button>
                <button type="button" class="btn btn-primary btn-transaction-edit"><i class="fa-solid fa-pencil"></i></button>
                <button type="button" class="btn btn-danger btn-transaction-delete"><i class="fa-regular fa-trash-can"></i></button>
            </td>
        </tr>
        <tr style="display:none;" class="transaction-details-container">
            <td colspan="3">
                @foreach($transaction->transacted_items as $item)
                    @include('transactions.transacted_items.details')
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $transactions->onEachSide(1)->links() !!}
