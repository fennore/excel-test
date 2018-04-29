<?php

namespace Fennore\Excel\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Fennore\Excel\Util\FileConverter;

class ExcelToPo extends Command
{
    private $fileConverter;

    public function __construct(FileConverter $fileConverter)
    {
        $this->fileConverter = $fileConverter;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('excel-to-po')
            ->setDescription('Converts excel file to po file')
            ->addArgument('excel-file', InputArgument::REQUIRED, 'The xlsx file to read from (ex. file/excel/example.xlsx)')
            ->addArgument('po-file', InputArgument::OPTIONAL, 'The po file to write to (ex. file/excel/example.xlsx)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $progress = $output->progress(100);
        $saveAs = $input->getArgument('po-file');

        $this->fileConverter->convertExcelToPo(
            $input->getArgument('excel-file'),
            $saveAs,
            $progress
        );

        $output->lightBlue()->bold()->out('Successfully created po file '.$saveAs);
    }
}
