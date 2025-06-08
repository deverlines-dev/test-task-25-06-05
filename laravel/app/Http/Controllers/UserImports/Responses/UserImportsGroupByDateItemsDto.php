<?php

namespace App\Http\Controllers\UserImports\Responses;

class UserImportsGroupByDateItemsDto
{
    public string $date;

    /**
     * @var array<int, UserImportsGroupByDateItemDto>
     */
    public array $items = [];
}
