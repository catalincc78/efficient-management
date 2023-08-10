<div>
    <div class="d-flex mb-2">
        <div class="transaction-cell-date ps-2"><b>{{ __('Date') }}</b></div>
        <div class="flex-fill text-end px-3 transaction-cell-amount"><b>{{ __('Amount') }}</b></div>
        <div class="text-center transaction-cell-actions"><b>{{ __('Actions') }}</b></div>
    </div>
    <div>
    @foreach($transactions as $transaction)
        <div class="transaction rounded-3 border mb-2" data-id="{{$transaction->id}}">
            <div class="d-flex align-items-center p-2">
                <div class="transaction-cell-date" >{{$transaction->created_at}}</div>
                <div class="flex-fill px-3 text-end transaction-cell-amount">{{number_format($transaction->total, 2)}}</div>
                <div class="text-end transaction-cell-actions">
                    <button type="button" class="btn btn-info btn-transaction-details" data-toggle="tooltip" title="Transaction Details">
                        <i class="fa-solid fa-list-ol"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-transaction-edit" data-toggle="tooltip" title="Edit Transaction">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-transaction-delete" data-toggle="tooltip" title="Delete Transaction">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
            <div style="display:none;" class="transaction-details-container p-0">
                <div class="p-0">
                    @foreach($transaction->transacted_items as $item)
                        @include('transactions.transacted_items.details')
                    @endforeach
                </div>
            </div>
        </div>

    @endforeach
    </div>
</div>
{!! $transactions->onEachSide(1)->links() !!}
