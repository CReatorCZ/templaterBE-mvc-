<?php

namespace App\Classes;

use Latte\Bridges\Tracy\TracyExtension;
use Latte\Engine;
use Latte\Loaders\StringLoader;
use Latte\Tools\Linter;
use Mpdf\Mpdf;

class CreatePDF
{
    public function createPDF($data): void{

        $latte = new Engine();
        $latte->setLoader(new StringLoader());

        $params = [
            'rival'=> $data->rival,
            'price'=> $data->price,
            'date'=> $data->date,
            'sector'=> $data->sector,
            'row'=> $data->row,
            'spot'=> $data->spot,
        ];

        $html = $latte->renderToString($data->html,$params);

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}