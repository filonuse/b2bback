<?php

namespace App\Exports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportCustomersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Название',
            'Номенклатура кол-во',
            'Поставки кол-во',
            'Сумма продаж',
            'Средний заказ',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['name'] . ', ' . $row['legal_name'],
            $row['count_nomenclature'],
            $row['count_supplies'],
            $row['amount'],
            $row['avg_amount'],
        ];
    }
}