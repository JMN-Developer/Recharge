<?php

namespace App\Exports;

use App\Models\RechargeHistory;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RechargeExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;
    protected $type;

    public function __construct($start_date, $end_date, $type)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->type = $type;

    }
    public function collection()
    {
        if ($this->type == 'all') {
            $orders = RechargeHistory::select('id', 'txid', 'number', 'created_at', 'type', 'amount', 'reseller_com')->where('reseller_id', Auth::user()->id)->whereBetween('created_at', [$this->start_date, $this->end_date])->cursor();
        } elseif ($this->type == 'International') {
            $orders = RechargeHistory::select('id', 'txid', 'number', 'created_at', 'type', 'amount', 'reseller_com')->where('type', '!=', 'Domestic')->where('type', '!=', 'pin')->where('reseller_id', Auth::user()->id)->whereBetween('created_at', [$this->start_date, $this->end_date])->cursor();
        } else {
            $orders = RechargeHistory::select('id', 'txid', 'number', 'created_at', 'type', 'amount', 'reseller_com')->where('type', '!=', 'International')->where('type', '!=', 'White Calling')->where('reseller_id', Auth::user()->id)->whereBetween('created_at', [$this->start_date, $this->end_date])->cursor();
        }
        $collection = collect();

        foreach ($orders as $order) {
            $collection->push([
                $order->txid,
                $order->number,
                $order->created_at,
                $order->type,
                $order->amount,
                $order->reseller_com,

            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        return ['TxId', 'Numero', 'Data', 'Genre', 'Importo', 'Profit'];
    }
}
