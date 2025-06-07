<?php

namespace App\Console\Commands;

use App\Services\Import\ImportUsersXlsxTable\ImportUsersXlsxTableService;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Throwable;

class ImportUsersXlsxTableCommand extends Command
{
    protected $signature = 'app:import-users-xlsx-table';

    protected $description = 'Импорт таблицы с пользователями';

    /**
     * @throws Throwable
     */
    public function handle(
        DatabaseManager $db,
        ImportUsersXlsxTableService $importUsersXlsxTableService
    ): void
    {
        $db->unsetEventDispatcher(); // отключаем события, что бы память не забилась

        $db->transaction(function () use ($importUsersXlsxTableService) {
            $importUsersXlsxTableService->import(storage_path('examples/backend-developer-fajl-dlya-importa-2024-05-29.xlsx'));
        });
    }
}
