<?php

namespace App\Repositories;

use App\Models\UserImport;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class UserImportRepository
{
    public function __construct(private DatabaseManager $db)
    {

    }

    /**
     * @return Builder<UserImport>
     */
    public function query(): Builder
    {
        return new UserImport()->newQuery();
    }

    /**
     * Получить данные с пагинацией, сгруппированные по дате
     *
     * @param int $page         Номер текущей страницы
     * @param int $perPage      Количество дат на страницу
     * @param string $direction Сортировки по дате (asc, desc)
     *
     * @return LengthAwarePaginator<Collection<int, UserImport>>
     */
    public function getPaginationGroupByDate(int $page, int $perPage, string $direction = 'asc'): LengthAwarePaginator
    {
        $dates = $this->query()
            ->select($this->db->raw('DATE(date)'))
            ->distinct()
            ->orderBy('date', $direction)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'));

        $items = $this->query()
            ->whereIn($this->db->raw('DATE(date)'), $dates)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy(fn(UserImport $item) => $item->getDate()->format('Y-m-d'));

        $totalDates = $this->query()
            ->select($this->db->raw('DATE(date)'))
            ->distinct()
            ->count();

        return new LengthAwarePaginator(
            items: $items,
            total: $totalDates,
            perPage: $perPage,
            currentPage: $page
        );
    }
}
