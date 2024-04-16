<?php

namespace Components\DataGrid;

class Template
{
    public static function getColumns(array $data): array
    {
        // Let's calculate the columns
        $totalColumns = [];
        foreach ($data as $arrays) {
            foreach ($arrays as $column => $data) {
                array_push($totalColumns, $column);
            }
        }
        // Now lets make the totalColumns unique
        return array_unique($totalColumns);
    }
    public static function render(string $title, array $data, bool $edit = true, bool $delete = true)
    {
        $totalCount = count($data);

        $html = '';

        $originalTitle = $title;

        $title = strtolower(str_replace(' ', '', $title . '-' . uniqid()));

        
    }
}
