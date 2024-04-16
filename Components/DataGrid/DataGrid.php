<?php

namespace Components\DataGrid;

use Components\Html;
use Components\Alerts;
use App\Security\CSRF;

class DataGrid
{
    public static function createTable(string $dbTable, array $data, string $theme, string $title = '', bool $edit = true, bool $delete = true): string
    {
        $totalCount = count($data);
        $csrfToken = CSRF::create();
        $html = '';
        if ($dbTable === '' || $dbTable === null) {
            $dbTable = 'table-' . uniqid();
        }
        $id = $dbTable . '-' . uniqid();
        $id = str_replace(' ', '', $id);
        $id = strtolower($id);
        if ($delete) {
        // The loading screen shown when deleting items
        $html .= '
            <div id="' . $id . '-delete-loading-screen" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-28 bg-slate-50 dark:bg-slate-600 hidden border border-black dark:border-slate-200">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-row">
                <p id="' . $id . '-delete-loading-screen-text" class="text-' . $theme . '-500">Deleting...</p>
                    <div role="status">
                        <svg aria-hidden="true" class="inline mx-2 w-8 h-8 text-gray-200 dark:text-white animate-spin fill-' . $theme . '-500 dark:fill-' . $theme . '-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                    </div>
                </div>
            </div>';
        }
        if ($totalCount < 1) {
            $html .= Alerts::danger('No results for "' . $title . '"');
            return $html;
        }
        // All encompassing div
        $html .= '<div class="my-4">';
        $html .= '<div class="ml-2 mt-4 text-gray-700 dark:text-gray-400">';
        $html .= HTML::h2($title, true);
        $html .= '<p>Results: <span id="' . $id . '-total">' . $totalCount . '</span></p>';
        $html .= '<p>Filtered: <span id="' . $id . '-filtered">' . $totalCount . '</span></p>';
        $html .= '<p>Selected: <span id="' . $id . '-selected">0</span></p>';
        $html .= '</div>';
        // Table
        $html .= '<div id="' . $id . '-container" class="m-4">';
        $html .= ($delete) ? '<form class="delete-selected-form">' : null;
        // The table loading div
        $html .= '<div id="' . $id . '-loading-table" class="mt-12 bg-' . $theme . '-500 h-full w-full text-center text-white">Data Loading... Please wait<svg class="inline mx-4 w-8 h-8 text-gray-200 dark:text-white animate-spin fill-blue-600 dark:fill-' . $theme . '-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg></div>';
        // Table itself
        $html .= '<table id="' . $id . '" class="hidden w-full bg-gray-100 dark:bg-gray-900 datagrid mt-8 p-8 text-gray-700 dark:text-gray-400 border-collapse border border-slate-400 text-center">';
        $html .= '<thead class="bg-gray-300 dark:bg-gray-600 sticky top-0 dark:text-gray-300 border-collapse">';
            $thClass = "p-2 border border-slate-400 cursor-pointer";
            $html .= '<tr>';
                // If delete is true, have a All th
                if ($delete) {
                    $html .= '<th scope="col" class="' . $thClass . '"><input type="checkbox" class="select-all" title="select all visible rows" /></th>';
                }
                // Let's calculate the columns
                $totalColumns = self::getColumns($data);
                foreach ($totalColumns as $cols) {
                    $html .= '<th scope="col" class="' . $thClass . '"><span>' . $cols . '</span> <span class="text-xs text-' . $theme . '-500">&#x25B2;&#x25BC;</span></th>';
                }
                // and one more th for the actions if enabled
                if ($delete || $edit) {
                    $html .= '<th scope="col" class="' . $thClass . '">Actions</th>';
                }
            $html .= '</tr>';
            // Now for the second thead row where the filters will stay
            $html .= '<tr>';
                foreach ($totalColumns as $col) {
                    $html .= '<th scope="col" class="' . $thClass . '"></th>';
                }
                if ($delete) {
                    $html .= '<th scope="col" class="' . $thClass . '"></th>';
                    $html .= '<th scope="col" class="' . $thClass . '"></th>';
                } elseif ($edit && !$delete) {
                    $html .= '<th scope="col" class="' . $thClass . '"></th>';
                }
            $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
            $counter = 0;
            // Time to loop through the data array and build the tbody
            foreach ($data as $indexes => $arrays) {
                $counter++;
                $currentId = $arrays['id'] ?? $counter;
                $html .= '<tr tabindex="' . $indexes . '" data-row-id="' . $currentId . '" class="focus:bg-' . $theme . '-500 focus:text-slate-900 no-paginate">';
                    $tdClass = 'max-w-lg p-4 border border-slate-400';
                    foreach ($arrays as $column => $value) {
                        if ($column === 'id' && $delete) {
                            $html .= '<td class="' . $tdClass . ' focus:text-white"><input type="checkbox" value="' . $currentId . '" name="row[]"></td>';
                        }
                        // Convert nulls or empty strings to (Empty) so it's easier to filter
                        if ($value === null || $value === '') {
                            $value = '(Empty)';
                        }
                        if ($column === 'id') {
                            $html .= '<td class="' . $tdClass . '" data-row-id="' . $value . '">' . $value . '</td>';
                        } else {
                            $html .= '<td class="' . $tdClass . '">' . htmlspecialchars($value) . '</td>';
                        }
                    }
                    if ($delete || $edit) {
                        // Whether we have delet or edit, we will do another <td>
                        $html .= '<td class="' . $tdClass . '">';
                            if ($delete) {
                                $html .= '<button data-table="' . $dbTable . '" data-id="' . $currentId . '" type="button" data-csrf="' . $csrfToken . '" class="delete ml-2 my-2 block text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Delete</button>';
                            }
                            if ($edit) {
                                $html .= '<button data-table="' . $dbTable . '" data-id="' . $currentId . '" data-columns="' . implode(',', $totalColumns) . '" data-csrf="' . $csrfToken . '" type="button" class="edit ml-2 my-2 block text-white dark:text-gray-900 bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-200 dark:hover:bg-gray-300 dark:focus:ring-gray-400">Edit</button>';
                            }
                        $html .= '</td>';
                    }
                $html .= '</tr>';
            }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        // Delete all modal starts here
        if ($delete) {
            // Delete selected button
            $html .= '<button id="' . $id . '-mass-delete-modal-trigger" class="delete-selected block ml-2 my-2 text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800" type="button" data-modal-toggle="' . $id . '-mass-delete-modal">
                Delete selected
            </button>';
            $html .= '
            <div id="' . $id . '-mass-delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 md:inset-0 h-modal md:h-full justify-center items-center" aria-hidden="true">
                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                    <div class="relative bg-white border border-gray-400 dark:border-gray-300 rounded-lg shadow dark:bg-gray-700">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="' . $id . '-mass-delete-modal">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-6 text-center">
                            <svg aria-hidden="true" class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 id="' . $id . '-mass-delete-modal-text" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this entry?</h3>
                            <button data-modal-toggle="' . $id . '-mass-delete-modal" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I\'m sure
                            </button>
                            <button data-modal-toggle="' . $id . '-mass-delete-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            ';
            // Show which table we are deleting from
            $html .= '<input type="hidden" name="deleteRecords" value="' . $dbTable . '" />';
            // Include the CSRF token
            $html .= '<input type="hidden" name="csrf_token" value="' . $csrfToken . '" />';
            // Close the mass delete form
            $html .= '</form>';
            // Export functionality
            $html .= '<div class="flex">';
            $exportButtonsArray = [
                'Export in CSV' => '/api/tools/export-csv',
                'Export in TSV' => '/api/tools/export-tsv'
            ];
            foreach ($exportButtonsArray as $name => $link) {
                $html .= '<form action="' . $link . '" method="post" target="_blank">';
                    $html .= '<button type="submit" class="ml-2 mt-2 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600" title="' . $name . '">';
                        $html .= $name;
                    $html .= '</button>';
                    $html .= '<input type="hidden" name="data" value="' . htmlentities(serialize($data)) . '" />';
                    $html .= '<input type="hidden" name="type" value="' . $id . '" />';
                    $html .= '<input type="hidden" name="csrf_token" value="' . $csrfToken . '" />';
                $html .= '</form>';
            }
            $html .= '</div>';
        }
        $html .= PHP_EOL;
        // Here we will build the skip columns array to instruct JS to skip making filters for certain columns. What we want is to skip the first column only when $delete is true, and always skip the last column + 1, the Actions column
        if ($delete) {
            $skipColumnArray = '[0, ' . (count($totalColumns) + 1) . ']';
        } elseif ($edit) {
            $skipColumnArray = '[' . (count($totalColumns) + 1) . ']';
        } else {
            $skipColumnArray = '[]';
        }

        $html .= <<<EOL
            <script nonce="1nL1n3JsRuN1192kwoko2k323WKE">
            $(document).ready(() => {
                const table = drawDataGrid('$id');
                buildDataGridFilters(table, '$id', $skipColumnArray);
                // On every re-draw, rebuild them
                table.on('draw', () => {
                    console.log(`redraw occured`);
                    buildDataGridFilters(table, '$id', $skipColumnArray);
                });
            });
            </script>
            EOL;
        // Close all encompassing div
        $html .= '</div>';
        return $html;
    }
    public static function getColumns($data): array
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
}
