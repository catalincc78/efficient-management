<table class="table table-borderless">
    <thead>
    <tr>
        <th scope="col">{{ __('Date') }}</th>
        <th scope="col" class="text-end">{{ __('Amount') }}</th>
        <th scope="col" class="text-center pe-2">{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr class="transaction p-0" data-id="{{$transaction->id}}">
            <td colspan="3" class="p-0">
                <div class="d-flex p-2 mt-2 ">
                    <div class="flex-fill pt-2" >{{$transaction->created_at}}</div>
                    <div class="flex-fill pt-2 text-end">{{number_format($transaction->total, 2)}}</div>
                    <div class="flex-fill text-end">
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
            </td>
        </tr>
        <tr style="display:none;" class="transaction-details-container p-0">
            <td class="p-0" colspan="3">
                <div class="border-start border-end border-bottom rounded-bottom-3">
                    @foreach($transaction->transacted_items as $item)
                        @include('transactions.transacted_items.details')
                    @endforeach
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $transactions->onEachSide(1)->links() !!}
