<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class MappingExport extends DefaultValueBinder implements FromArray, ShouldAutoSize, WithEvents, WithCustomValueBinder
{
    protected $records;

	public function __construct(array $records)
    {
        $this->records = $records;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return tr_benefit_adjustment::all();
    // }

    public function registerEvents(): array
	{
	    return [
	        AfterSheet::class    => function(AfterSheet $event) {

	        	// $cellRange = 'A1:U'.$event->sheet->getDelegate()->getHighestRow(); 
                //   	$event->sheet->getDelegate()->getStyle($cellRange)
	           //        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

	            // Set A1:D4 range to wrap text in cells
	            // $event->sheet->getDelegate()->getStyle('U1:U'.$event->sheet->getDelegate()->getHighestRow())
	            //     ->getAlignment()->setWrapText(true);

	            // $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(50);
	        },
	    ];
	}


    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return $this->records;
    }
}
