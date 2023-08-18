<?php

namespace App\Presenters;

use App\Classes\CreatePerson;
use App\Classes\DeletePerson;
use App\Classes\ExportDatabase;
use App\Classes\GetList;
use App\Classes\ImportDatabase;
use App\Classes\MyValidators;
use App\Classes\UpdatePerson;
use App\Classes\CreatePDF;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class EventsPresenter extends Presenter
{
    #[Inject]
    public GetList $getList;
    #[Inject]
    public CreatePerson $createPerson;
    #[Inject]
    public DeletePerson $deletePerson;
    #[Inject]
    public UpdatePerson $updatePerson;
    #[Inject]
    public CreatePDF $createPDF;
    #[Inject]
    public MyValidators $myValidators;
    #[Inject]
    public ExportDatabase $exportDatabase;
    #[Inject]
    public ImportDatabase $importDatabase;


    private array $days = ["Pondělí" => "Pondělí",
        "Úterý" => "Úterý",
        "Středa" => "Středa",
        "Čtvrtek" => "Čtvrtek",
        "Pátek" => "Pátek"];

    private string $defaultTextareaText = '<style>
.param{
text-align: center;
font-weight: bold;
}
.param.rival{
width: 400px;
margin-left: 240px;
height: 40px;
line-height: 40px;
font-size: 20px;
margin-bottom:1px;
}
.param.price{
margin-left: 240px;
height: 40px;
line-height: 40px;
width: 100px;
font-size: 20px;
margin-bottom:2px;
}
.param.date{
margin-left: 240px;
height: 40px;
line-height: 40px;
width: 150px;
font-size: 22px;
margin-bottom: 3px;
}
.param.sector{
width: 80px;
height: 38px;
margin-left: 225px;
float: left;
font-size: 26px;
}
.param.row{
margin-left: 65px;
width:80px;
height: 38px;
float: left;
font-size: 26px;
}
.param.spot{
margin-left: 80px;
width: 80px;
height: 38px;
float: left;
font-size: 26px;
}
</style>
<div style="background-image: 
url(\'https://s3.eu-central-1.amazonaws.com/enigoo/hcpce/d9b5cd07bce2353bf2544bd65caace04_1535975027315.jpg\');
background-repeat: no-repeat;
background-size: contain; 
width: 955px;
height: 373px;
overflow: hidden;
"> 
<div class="param" style="width:100%;height: 50px"></div>
<div class="param rival">{$rival}</div>
<div class="param price">{$price}</div>
<div class="param date">{$date}</div>
<div class="param sector">{$sector}</div>
<div class="param row">{$row}</div>
<div class="param spot">{$spot}</div>
</div>';


    private function getMenuItems(): array
    {
        return [
            ['title' => 'Default', 'link' => $this->link('default')],
            ['title' => 'Form', 'link' => $this->link('form')],
            ['title' => 'Database', 'link' => $this->link('database')],
        ];
    }

    public function startup()
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    public function renderDefault(): void
    {
        $this->template->add('menuItems', $this->getMenuItems());
    }

    public function actionDefault(): void
    {
        $str = "ahoj";
        $this->template->result = $str;
//        $this->template->variable = (object)["name"=>"ahoj"];

    }

    public function actionForm(?int $id): void
    {
//      http://project.local.cz/events/form
    }

    public function actionDatabase(): void
    {

        if (!$this->getUser()->isLoggedIn()) {
            try {
                $this->redirect('Sign:in');
            } catch (AbortException $e) {
            }
        }

        $this->template->variable = $this->getList->getList();
    }

    public function createComponentRegistrationForm(): Form
    {
        $id = null;
        if ($this->getParameter('id')) {
            $id = $this->getParameter('id');
        }

        $form = new Form;

        $form->addHidden('id');

        $form->addText('name', 'Jméno')
            ->addRule($form::LENGTH, "Invalid length of name", [2, 15])
            ->setRequired('Please fill your name.');

        $form->addText('email', 'E-mail')
            ->setRequired("Please fill your email")
            ->addRule($form::Email, 'Not a valid email')
            ->addRule($form::LENGTH, "Invalid length of email", [5, 30])
            ->addRule([$this->myValidators, "validateEmailDuplicity"], 'Email already exists.', (int)$id);

        $form->addInteger('age', 'Věk')
            ->addRule($form::Range, 'Please put a valid age', [0, 120])
            ->setRequired("Please fill your age");

        $form->addSelect("day", "Den ", $this->days)
            ->setPrompt("Zvolte den")
            ->setRequired("Please choose a day");

        $form->addSubmit('send', 'Odeslat');

        //$form->onValidate = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'formSucceeded'];
        return $form;
    }

    public function formSucceeded(Form $form, $data): void
    {
        $this->flashMessage($form, "info");

        if ($data->id) {
            $this->updatePerson->updatePerson($data->id, $data);
        } else {
            $this->createPerson->setPerson($data);
        }

        $this->redirect('Events:database');
    }

    public function createComponentPdfForm(): Form
    {
        $form = new Form;

        $form->addText('rival', 'Soupeř')->setDefaultValue('Velkej kladenskej hokej Liberec');
        //->addRule($form::LENGTH, "Invalid length of name", [2, 15])
        //->setRequired('Prosím doplňtě soupeře.');

        $form->addText('price', 'Cena')->setDefaultValue('58400');;
        //->setRequired('Prosím doplňtě cenu.');

        $form->addText('date', 'Datum')->setDefaultValue('22.22.2000');;
        //->setRequired('Prosím doplňtě datum.');

        $form->addText('sector', 'Sektor')->setDefaultValue('8B');;
        //->setRequired('Prosím doplňtě sektor.');

        $form->addText('row', 'Řada')->setDefaultValue('85');;
        //->setRequired('Prosím doplňtě řadu.');

        $form->addText('spot', 'Místo')->setDefaultValue('44');;
        //->setRequired('Prosím doplňtě místo.');

        $form->addTextArea('html', "html")
//            ->addRule($form::LENGTH, "Invalid length of name", [0, 150])
//            ->setRequired('Please fill textarea.')
//            ->setHtmlAttribute('style', 'width: 800px; height:800px')
            ->setDefaultValue($this->defaultTextareaText);

        $form->addSubmit('send', 'Odeslat')
            ->setHtmlAttribute('class', 'btn btn-primary');


        $form->onSuccess[] = [$this, 'pdfFormSucceeded'];
        return $form;
    }

    public function pdfFormSucceeded(Form $form, $data): void
    {
        $this->createPDF->createPDF($data);
    }

    public function actionUpdate(int $id): void
    {
        $person = $this->updatePerson->getPerson($id);
        $this->template->person = $person;
        $this['registrationForm']->setDefaults([
            'id' => $id,
            'name' => $person->firstName,
            'email' => $person->email,
            'age' => $person->age,
            'day' => $person->day]);
    }

    public function handleDelete(int $id): void
    {
        $this->deletePerson->deletePerson($id);
        $this->redirect('Events:database');
    }

    public function actionPdf(): void
    {

    }

    public function createComponentImportDatabase(): Form
    {
        $form = new Form;

        $form->addUpload('file')->setRequired();

        $form->addSubmit('send');

        $form->onSuccess[] = [$this, 'importDatabaseSucceeded'];
        return $form;
    }

    public function importDatabaseSucceeded(Form $form, $data): void
    {
        $this->flashMessage(getcwd());

        if ($file = $this->getHttpRequest()->getFiles()['file']) {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $allData = [];

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $this->flashMessage($cell);
                    $rowData[] = $cell->getValue();
                }
                $this->flashMessage('-----------*---------');
                $allData[] = $rowData;
            }

            $this->importDatabase->setDatabase($allData);
            $this->redirect("Events:Database");
        }
    }

    public function handleExportDatabase($data): void
    {
        //TODO $data instead of getList

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ],
            ]
        ];
        $spreadsheet = new Spreadsheet();
        $arrayData = [
            ['Jméno', 'Email', 'Věk', 'Den', 'ID'],
        ];
        foreach ($this->getList->getList() as $row) {
            $arrayData[] = [
                $row->firstName,
                $row->email,
                $row->age,
                $row->day,
                $row->id,
            ];
        }
        $spreadsheet->getActiveSheet()
            ->fromArray($arrayData, NULL, 'B2')
            ->getStyle('B2:F2')
            ->applyFromArray($styleArray);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="database.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function actionCurl(): void
    {
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/getallmanufacturers?format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        $this->template->data = $data['Results'];
    }
}
