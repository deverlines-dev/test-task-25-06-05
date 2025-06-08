<?php

namespace App\Http\Controllers\UserImports;

use App\Http\Controllers\AbstractController;
use App\Http\Controllers\UserImports\Responses\UserImportsGroupByDateItemDto;
use App\Http\Controllers\UserImports\Responses\UserImportsGroupByDateItemsDto;
use App\Http\Controllers\UserImports\Responses\UserImportsGroupByDateResponseDto;
use App\Models\UserImport;
use App\Repositories\UserImportRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

readonly class UserImportsController extends AbstractController
{
    public function __construct(private UserImportRepository $userImportRepository)
    {

    }

    public function groupByDate(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 30);

        if ($perPage > 100) {
            $perPage = 100;
        }

        $items = $this->userImportRepository->getPaginationGroupByDate(
            page: $page,
            perPage: $perPage,
        );

        $responseDto = new UserImportsGroupByDateResponseDto();

        $items->each(function (Collection $userImportCollection, string $date) use ($responseDto) {

            $items = new UserImportsGroupByDateItemsDto();
            $items->date = $date;

            $userImportCollection->each(function (UserImport $userImport) use ($items) {

                $item = new UserImportsGroupByDateItemDto();
                $item->extId = $userImport->getId();
                $item->name = $userImport->getName();

                $items->items[] = $item;

            });

            $responseDto->items[] = $items;

        });

        return new JsonResponse($responseDto->items);
    }
}
