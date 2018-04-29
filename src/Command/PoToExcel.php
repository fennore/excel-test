<?php

namespace Fennore\Excel\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Fennore\Excel\Util\FileConverter;

class PoToExcel extends Command
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
            ->setName('po-to-excel')
            ->setDescription('Converts po file to excel file')
            ->addArgument('po-file', InputArgument::REQUIRED, 'The po file to read from (ex. file/excel/example.po)')
            ->addArgument('excel-file', InputArgument::OPTIONAL, 'The excel file to write to (ex. file/excel/example.xlsx)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $progress = $output->progress(100);
        $saveAs = $input->getArgument('excel-file');

        $this->fileConverter->convertPoToExcel(
            $input->getArgument('po-file'),
            $saveAs,
            $progress
        );
        $output->lightBlue()->bold()->out('Successfully created po file '.$saveAs);
    }
}
