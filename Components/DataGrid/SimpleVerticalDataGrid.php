<?php

namespace Components\DataGrid;

use Components\Alerts;

class SimpleVerticalDataGrid extends DataGrid
{
    public static function render(array $data) : string
    {
        $html = '';
        $tableId = uniqid();
        if (empty($data)) {
            $html .= Alerts::danger('No data found');
            return $html;
        }
        $html .= '<div class="m-6 overflow-x-auto relative shadow-md max-h-[44rem]">';
            $html .= '<table id="' . $tableId . '" class="bg-gray-100 dark:bg-gray-800 w-full p-8 text-gray-700 dark:text-gray-400 border-collapse border border-slate-400 text-center">';
                $totalColumns = self::getColumns($data);
                $html .= '<thead>';
                    $html .= '<tr class="bg-gray-200 dark:bg-gray-700">';
                        if (!empty($totalColumns)) {
                            foreach ($totalColumns as $column) {
                                $html .= '<th class="py-4 px-6 border border-slate-400">' . htmlspecialchars($column) . '</th>';
                            }
                        }
                    $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                $tdClass = 'py-4 px-6 border border-slate-400 max-w-lg break-all';
                foreach ($data as $index => $array) {
                    if (is_array($array)) {
                        $html .=  '<tr tabindex="' . $index . '" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 focus:bg-gray-200 dark:focus:bg-gray-400 focus:text-black dark:focus:text-white focus:dark:border-gray-400">';
                            foreach ($array as $col => $val) {
                                $val = ($val !== null) ? htmlspecialchars($val) : 'null';
                                $html .= '<td class="' . $tdClass . '" title="' . $val . '">' . $val . '</td>';
                            }
                        $html .= '</tr>';
                    } else {
                        $html .=  '<tr tabindex="' . $index . '" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 focus:bg-gray-200 dark:focus:bg-gray-400 focus:text-black dark:focus:text-white focus:dark:border-gray-400">';
                            $html .= '<td class="' . $tdClass . '" title="' . $index . '">' . ($index !== null) ? htmlspecialchars($index) : 'null' . '</td>';
                            $html .= '<td class="' . $tdClass . '" title="' . $array . '">' . ($array !== null) ? htmlspecialchars($array) : 'null' . '</td>';
                        $html .= '</tr>';
                    }
                }
                $html .= '</tbody>';
            $html .= '</table>';
        $html .= '</div>';
        return $html;
    }
}
