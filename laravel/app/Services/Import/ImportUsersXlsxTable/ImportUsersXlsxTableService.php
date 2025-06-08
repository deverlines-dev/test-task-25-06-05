<?php

namespace App\Services\Import\ImportUsersXlsxTable;

use App\Events\Broadcasts\UserImportRowsCreatedEvent;
use App\Services\Import\ImportXlsxService;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use Symfony\Component\Console\Output\ConsoleOutput;

readonly class ImportUsersXlsxTableService
{
    public function __construct(
        private ImportXlsxService $importXlsxService,
        private ImportUsersXlsxTableValidatorService $tableValidatorService,
        private ImportUsersXlsxTableValueValidatorService $tableValueValidatorService,
        private DatabaseManager $db,
        private LogManager $log,
        private RedisManager $redis
    )
    {

    }

    /**
     *
     * @throws IOException
     * @throws Exception
     */
    public function import(string $path): void
    {
        $uuid = Str::uuid()->toString();

        $console = new ConsoleOutput();
        $consoleMessageSection = $console->section();
        $consoleTimeSection = $console->section();
        $consoleMemorySection = $console->section();
        $consoleCountSection = $console->section();

        $this->tableValidatorService->validateFileExists($path);

        $tableHeaders = [];
        $tableErrors = [];

        $countTotalItems = 0;
        $countSavingItems = 0;
        $countContinueItems = 0;
        $timeExecution = hrtime(true);

        $consoleMessageSection->writeln("Старт импорта");
        $consoleTimeSection->overwrite("time: " . (hrtime(true) - $timeExecution) / 1e+9 . ' sec');
        $consoleMemorySection->overwrite("memory: " . memory_get_usage() / 1024 / 1024 . " / " . memory_get_peak_usage() / 1024 / 1024 . ' mb');
        $consoleCountSection->overwrite("saving: 0 ok, 0 continue, 0 total");

        /**
         * @var Row $row
         */
        foreach ($this->importXlsxService->import(path: $path, chunkSize: 5000) as $rows) {

            $dbItems = [];

            foreach ($rows as $rowNumber => $row) {

                $cells = $row->getCells();

                if ($rowNumber === 1) {
                    $tableHeaders = $this->getTableHeaderValues($cells);

                    continue;
                }

                $tableItem = $this->importXlsxService->getTableItemWithColumnsName($cells, $tableHeaders);

                $errors = $this->tableValueValidatorService->getTableItemValidationErrors($tableItem);
                if ($errors) {
                    $tableErrors[$rowNumber] = $this->errorFormat($rowNumber, $errors);

                    $countContinueItems++;
                    $countTotalItems++;

                    $this->log->driver('import')->warning($tableErrors[$rowNumber]);

                    continue;
                }

                $dbItems[] = $this->tableItemToDBItem($tableItem);
            }

            $chunkCount = count($dbItems);

            $countSavingItems += $chunkCount;
            $countTotalItems  += $chunkCount;

            $this->db->table('user_imports')->insert($dbItems);

            $consoleTimeSection->overwrite("time: " . (hrtime(true) - $timeExecution) / 1e+9 . ' sec');
            $consoleMemorySection->overwrite("memory: " . memory_get_usage() / 1024 / 1024 . " / " . memory_get_peak_usage() / 1024 / 1024 . ' mb');
            $consoleCountSection->overwrite("saving: $countSavingItems ok, $countContinueItems continue, $countTotalItems total");

            $key = now()->format('Y_m_d_H_i_s_u');
            $this->redis->connection('queue')->set("import_status:$uuid:$key", $countTotalItems);

            event(new UserImportRowsCreatedEvent(
                uuid: $uuid,
                rows: $countTotalItems
            ));
        }
    }

    /**
     *
     * Переводит значение из xlsx таблицы в значения из БД таблицы
     *
     * @param array<int, string> $chunkItem
     *
     * @return array<int, string> $chunkItem
     */
    private function tableItemToDBItem(array $chunkItem): array
    {
        return [
            'ext_id' => (int)$chunkItem['id'],
            'name'   => $chunkItem['name'],
            'date'   => $this->dateToSqlFormat($chunkItem['date']),
        ];
    }

    private function dateToSqlFormat(string $date): string
    {
        list($day, $month, $year) = explode('.', $date);

        $day   = str_pad($day, 2, '0', STR_PAD_LEFT);
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        return "$year-$month-$day";
    }

    /**
     * @param array<int, array<string>> $errors
     */
    private function errorFormat(int $rowNumber, array $errors): string
    {
        $errorsAsString = $rowNumber . ':';
        foreach ($errors as $columnName => $columnErrors) {
            if (empty($columnErrors)) {
                continue;
            }

            $errorsAsString .= " $columnName: " . implode(", ", $columnErrors);
        }

        return $errorsAsString;
    }

    /**
     * @param array<int, Cell> $cells
     *
     * @return array<int, string>
     *
     * @throws Exception
     */
    private function getTableHeaderValues(array $cells): array
    {
        $tableHeaders = $this->importXlsxService->getTableHeaders($cells);

        $this->tableValidatorService->validateTableHeaders(
            tableHeaders: $tableHeaders,
            requiredHeaders: $this->getRequiredHeaders()
        );

        return $tableHeaders;
    }

    private function getRequiredHeaders(): array
    {
        return ['id', 'name', 'date'];
    }
}
