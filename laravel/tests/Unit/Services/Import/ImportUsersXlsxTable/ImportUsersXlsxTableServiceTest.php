<?php

namespace Tests\Unit\Services\Import\ImportUsersXlsxTable;

use App\Services\Import\ImportUsersXlsxTable\ImportUsersXlsxTableService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\DatabaseManager;
use OpenSpout\Common\Exception\IOException;
use Tests\TestCase;

class ImportUsersXlsxTableServiceTest extends TestCase
{
    private ImportUsersXlsxTableService $importUsersXlsxTableService;

    private DatabaseManager $db;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->importUsersXlsxTableService = $this->app->make(ImportUsersXlsxTableService::class);
        $this->db = $this->app->make(DatabaseManager::class);
    }

    /**
     * @throws IOException
     */
    public function testImport(): void
    {
        $beforeInsertCount = $this->db->table('user_imports')->count();

        $this->importUsersXlsxTableService->import(storage_path('examples/test-success-backend-developer-fajl-dlya-importa-2024-05-29.xlsx'));

        $afterInsertCount = $this->db->table('user_imports')->count();

        $this->assertEquals($afterInsertCount - 999, $beforeInsertCount);
    }
}
