<x-printable-report
    title="Inventory Transactions Report"
    :data="$inventoryTransactions->map(function($transaction) {
        return [
            'item_name' => $transaction->item->name,
            'transaction_type' => $transaction->transaction_type,
            'quantity' => $transaction->quantity,
            'from_location' => $transaction->from_location,
            'to_location' => $transaction->to_location,
            'performed_by' => $transaction->performedBy->first_name . ' ' . $transaction->performedBy->last_name,
            'created_at' => $transaction->created_at,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'item_name', 'label' => 'Item'],
        ['key' => 'transaction_type', 'label' => 'Type'],
        ['key' => 'quantity', 'label' => 'Quantity'],
        ['key' => 'from_location', 'label' => 'From'],
        ['key' => 'to_location', 'label' => 'To'],
        ['key' => 'performed_by', 'label' => 'Performed By'],
        ['key' => 'created_at', 'label' => 'Date'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Transactions Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>