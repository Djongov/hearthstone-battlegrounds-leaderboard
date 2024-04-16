/* DataGrid DataTable

Here are the functions that are for DataGrid display. Using DataTables library

*/
const countAllCheckedCheckboxes = (tableId) => {
    return document.querySelectorAll(`#${tableId} > tbody > tr > td > input[type="checkbox"]:checked`).length;
}

const countVisibleRows = (tableId) => {
    return document.querySelectorAll(`#${tableId} > tbody > tr:not([style*="display: none;"])`).length;
}

const tables = document.querySelectorAll(`table.datagrid`);

if (tables.length > 0) {
    tables.forEach(table => {
        // Let's get some elements we will be using
        const tableId = table.id;
        // Now the results elements
        let totalResults = document.getElementById(tableId + '-total');
        let selectedResults = document.getElementById(tableId + '-selected');
        let filteredResults = document.getElementById(tableId + '-filtered');
        filteredResults.innerText = countVisibleRows(tableId);
        // Now the modal elements for the mass delete
        const massDeleteModalTriggerer = document.getElementById(tableId + '-mass-delete-modal-trigger');
        const massDeleteModalText = document.getElementById(tableId + '-mass-delete-modal-text');
        const deleteLoadingScreen = document.getElementById(tableId + '-delete-loading-screen');
        const deleteLoadingScreenText = document.getElementById(tableId + '-delete-loading-screen-text');

        // First the Edit button and get records
        const editButtons = document.querySelectorAll(`#${tableId} button.edit`);

        if (editButtons.length > 0) {
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const uniqueId = generateUniqueId(4);
                    // Generate the modal
                    let modal = editModal(uniqueId);
                    // Insert the modal at the bottom of the first div after the body
                    document.body.insertBefore(modal, document.body.firstChild);
                    // Now show the modal
                    modal.classList.remove('hidden');
                    // Now let's disable scrolling on the rest of the page by adding overflow-hidden to the body
                    document.body.classList.add('overflow-hidden');
                    // Blur the body excluding the modal
                    toggleBlur(modal);
                    // Let's make some bindings
                    const modalResult = document.getElementById(`${uniqueId}-result`);
                    // First, the close button
                    const closeXButton = document.getElementById(`${uniqueId}-x-button`);
                    // The cancel button
                    const cancelButton = document.getElementById(`${uniqueId}-close-button`);
                    // Modal Save button
                    const saveButton = document.getElementById(`${uniqueId}-edit`);
                    const cancelButtonsArray = [closeXButton, cancelButton];
                    cancelButtonsArray.forEach(cancelButton => {
                        cancelButton.addEventListener('click', () => {
                            // Completely remove the modal
                            modal.remove();
                            // Return the overflow of the body
                            document.body.classList.remove('overflow-hidden');
                            // Remove the blur by toggling the blur class
                            toggleBlur(modal);
                        })
                    })
                    let modalBody = document.getElementById(`${uniqueId}-body`);
                    let responseStatus = 0;
                    // Now let's fetch data from the API and populate the modalBody. We need to send the id and the table name
                    const formData = new FormData();
                    // The Edit button has a data-columns attribute that contains the columns we need to fetch, so we want to send them to the API along with the id and table name
                    formData.append('columns', button.dataset.columns);
                    formData.append('table', button.dataset.table);
                    formData.append('id', button.dataset.id);
                    formData.append('csrf_token', button.dataset.csrf);
                    fetch('/api/datagrid/get-records', {
                        method: 'POST',
                        headers: {
                            'secretHeader': 'badass',
                            'X-CSRF-TOKEN': button.dataset.csrf
                        },
                        body: formData,
                        redirect: 'manual'
                    }).then(response => {
                        if (response.status === 0 || response.status === 401 || response.status === 403) {
                            location.reload();
                        }
                        // If response is JSON, then we probably got an error because we expect html from this request
                        if (response.headers.get('content-type').includes('application/json')) {
                            // Let's disable the save button
                            saveButton.disabled = true;
                            response.json().then(errorData => {
                                modalBody.innerHTML = `<p class="ml-4 w-fit font-bold text-red-500">${errorData.data}</p>`;
                            }).catch(error => {
                                modalBody.innerHTML = `<p class="ml-4 w-fit font-bold text-red-500">Error occurred, please try again later.</p>`;
                            });
                        }
                        return response.text();
                    }).then(text => {
                        modalBody.innerHTML = '';
                        if (deleteLoadingScreen) {
                            deleteLoadingScreen.classList.add('hidden');
                        }
                        modalBody.innerHTML = text;
                    });
                    // Now the edit button
                    const initialButtonText = saveButton.textContent;
                    saveButton.addEventListener('click', () => {
                        // transform the button text to a loader
                        saveButton.innerHTML = loaderString();
                        // Build the body of the update request. We need to go through all the inputs and get their values
                        const formData = new FormData();
                        // Loop through all of the modalBody inputs, textarea and select and save them to the formData
                        const modalBodyInputs = modal.querySelectorAll(`input, textarea, select`);
                        modalBodyInputs.forEach(input => {
                            formData.append(input.name, input.value);
                        });
                        // Now let's take care of potential checkboxes
                        const modalBodyCheckboxes = modal.querySelectorAll(`input[type=checkbox]`);
                        // Loop through the checkboxes and if they are checked, we transmit the value as 1, else as 0
                        modalBodyCheckboxes.forEach(checkbox => {
                            formData.append(checkbox.name, checkbox.checked ? 1 : 0);
                        });
                        // Now let's send a fetch request to /api/process with form-data api-action and the apiAction value
                        fetch('/api/datagrid/update-records', {
                            method: 'POST',
                            headers: {
                                'secretHeader': 'badass',
                                'X-CSRF-TOKEN': formData.get('csrf_token')
                            },
                            body: formData,
                            redirect: 'manual'
                        }).then(response => {
                            responseStatus = response.status;
                            if (responseStatus === 403 || responseStatus === 401 || responseStatus === 0) {
                                modalBody.innerHTML = `<p class="text-red-500 font-semibold">Response not ok, refreshing</p>`;
                                location.reload();
                            } else {
                                return response.json()
                            }
                        }).then(json => {
                            // So if the response is status is 404 on the update, it mans nothing got updated, so we display it.
                            if (responseStatus >= 400) {
                                saveButton.innerText = 'Retry';
                                let errorMessage = '';
                                if (json.data) {
                                    errorMessage = json.data;
                                } else {
                                    errorMessage = JSON.stringify(json);
                                }
                                modalResult.innerHTML = `<p class="text-red-500 font-semibold">${errorMessage}</p>`;
                            } else {
                                saveButton.innerText = initialButtonText;
                                modalResult.innerHTML = `<p class="text-green-500 font-semibold">${json.data}</p>`;
                                location.reload();
                            }
                        }).catch(error => {
                            console.error('Error during fetch:', error);
                        });
                    })
                })
            });
        }
        // Disable it after Flowbite's JS
        if (massDeleteModalTriggerer) {
            massDeleteModalTriggerer.disabled = true;
        }
        // Get all the checkboxes in the table
        const allTableCheckboxes = document.querySelectorAll(`#${tableId} > tbody > tr > td > input[type=checkbox]`);
        if (allTableCheckboxes.length > 0) {
            allTableCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    // make row focused, 2 parent nodes behind
                    if (checkbox.checked) {
                        checkbox.parentNode.parentNode.classList.add('bg-gray-200', 'text-black');
                    } else {
                        checkbox.parentNode.parentNode.classList.remove('bg-gray-200', 'text-black');
                    }
                    const allTableCheckedCheckboxes = countAllCheckedCheckboxes(tableId);
                    selectedResults.innerText = allTableCheckedCheckboxes;
                    filteredResults.innerText = countVisibleRows(tableId);
                    if (massDeleteModalTriggerer && massDeleteModalText) {
                        massDeleteModalText.innerText = 'Are you sure you want to delete ' + allTableCheckedCheckboxes + ' entries?';
                        massDeleteModalTriggerer.disabled = (allTableCheckedCheckboxes > 0) ? false : true;
                    }
                }, false);
            })
        }
        // Now the mass delete functionality

        // Find the mass delete form, which is in the container and has a class of delete-selected-form
        const deleteForms = document.querySelectorAll(`#${tableId}-container > form.delete-selected-form`);

        if (deleteForms.length > 0) {
            deleteForms.forEach(form => {
                form.addEventListener('submit', (event) => {
                    deleteLoadingScreen.classList.remove('hidden');
                    event.preventDefault();
                    const data = new URLSearchParams(new FormData(form));
                    let responseStatus = 0;
                    fetch('/api/datagrid/delete-records', {
                        method: 'post',
                        // Let's send this secret header
                        headers: {
                            'secretHeader': 'badass',
                            'X-CSRF-TOKEN': data.get('csrf_token')
                        },
                        body: data,
                        redirect: 'manual'
                    }).then(response => {
                        responseStatus = response.status;
                        if (responseStatus === 0 || responseStatus === 401 || responseStatus === 403) {
                            location.reload();
                        }
                        return response.json();
                    }).then(json => {
                        if (responseStatus > 400) {
                            deleteLoadingScreenText.innerHTML = `<p class="text-red-500 font-semibold">${json.data}</p>`;
                            return;
                        }
                        // Assuming we get here is all good, quickly display the success message and reload the page
                        deleteLoadingScreenText.innerHTML = `<p class="text-green-500 font-semibold">${json.data}</p>`;
                        location.reload();
                    });
                }, false);
            })
        }

        // Individual delete buttons
        const deleteButtons = document.querySelectorAll(`#${tableId} > tbody > tr > td > button.delete`);

        if (deleteButtons.length > 0) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    // inser the modal after the table
                    let modal = deleteModal(tableId, event.target.dataset.id + ' in table ' + event.target.dataset.table);
                    // Insert the modal at the bottom of the first div after the body
                    document.body.insertBefore(modal, document.body.firstChild);
                    // Now show the modal
                    modal.classList.remove('hidden');
                    // Now let's disable scrolling on the rest of the page by adding overflow-hidden to the body
                    document.body.classList.add('overflow-hidden');
                    // Blur the body excluding the modal
                    toggleBlur(modal);
                    // First, the close button
                    const closeXButton = document.getElementById(`${tableId}-x-button`);
                    // The cancel button
                    const cancelButton = document.getElementById(`${tableId}-close-button`);
                    const cancelButtonsArray = [closeXButton, cancelButton];
                    cancelButtonsArray.forEach(cancelButton => {
                        cancelButton.addEventListener('click', () => {
                            // Completely remove the modal
                            modal.remove();
                            // Return the overflow of the body
                            document.body.classList.remove('overflow-hidden');
                            // Remove the blur by toggling the blur class
                            toggleBlur(modal);
                        })
                    })
                    const deleteModalButton = document.getElementById(`${tableId}-delete`);
                    let modalBody = document.getElementById(`${tableId}-body`);
                    let responseStatus = 0;
                    deleteModalButton.addEventListener('click', () => {
                    // transform the button text to a loader
                    deleteModalButton.innerHTML = loaderString();
                    // Build the body of the delete request
                    const formData = new URLSearchParams();
                    formData.append('id', event.target.dataset.id);
                    formData.append('table', event.target.dataset.table);
                    formData.append('csrf_token', event.target.dataset.csrf);
                    fetch('/api/datagrid/delete-records', {
                        method: 'POST',
                        headers: {
                            'secretHeader': 'badass',
                            'X-CSRF-TOKEN': event.target.dataset.csrf
                        },
                        body: formData,
                        redirect: 'manual'
                    }).then(response => {
                        responseStatus = response.status;
                        if (responseStatus === 403 || responseStatus === 401 || responseStatus === 0) {
                            modalBody.innerHTML = `<p class="text-red-500 font-semibold">${json.data}</p>`;
                            location.reload();
                        } else {
                            return response.json()
                        }
                    }).then(json => {
                        // Return the overflow of the body
                        document.body.classList.remove('overflow-hidden');
                        // Remove the blur by toggling the blur class
                        toggleBlur(modal);
                        if (responseStatus === 200) {
                            // if we are deleting from a table, Delete the closest row
                            const closestRow = button.closest('tr');
                            if (closestRow) {
                                closestRow.remove();
                            }
                            // Decrement the total results
                            totalResults.innerText = parseInt(totalResults.innerText) - 1;
                            // Decrease the filtered results if the row is visible
                            if (closestRow.style.display !== 'none') {
                                filteredResults.innerText = parseInt(filteredResults.innerText) - 1;
                            }
                            // Completely remove the modal
                            modal.remove();
                        } else {
                            deleteModalButton.textContent = 'Retry';
                            modalBody.innerHTML = `<p class="text-red-500 font-semibold">${json.data}</p>`;
                        }
                    }).catch(error => {
                        console.error('Error during fetch:', error);
                    })
                })
                }, false);
            });
        }

        /* Check all checkboxes functionality */
        // Get the "All" checkbox in the table
        // So basically find the checkbox all checkbox and bind a click event on it
        $(document).on("change", `#${tableId} input.select-all`, function (event) {
            //$(`#${tableId} > thead > tr > th > input[type=checkbox].select-all`).click(function(event) {
            // Find all the visible checkboxes, jquery returns an object so we user Object.entries() to conver to array so we can forEach it and get through the individual rows in the table
            Object.entries($(`#${tableId} tbody tr:visible :checkbox`).prop('checked', this.checked).closest('tr')).forEach(([index, row]) => {
                // Weirdly jquery returns an extra key in the array that's not an html element, so it throws errors unless we do this check
                if (row instanceof Element) {
                    // If select all checkbox is checked, change the color of the row
                    if (event.target.checked) {
                        row.classList.add('bg-gray-200', 'text-black');
                    } else {
                        // Else remove the extra classes
                        row.classList.remove('bg-gray-200', 'text-black');
                    }
                }
            });
            // After this action, we need to update the "Selected" count, as well as tell the modal how many rows we have selected         
            const allTableCheckedCheckboxes = countAllCheckedCheckboxes(tableId);
            selectedResults.innerText = allTableCheckedCheckboxes;
            if (massDeleteModalTriggerer && massDeleteModalText) {
                massDeleteModalText.innerText = 'Are you sure you want to delete ' + allTableCheckedCheckboxes + ' entries?';
                massDeleteModalTriggerer.disabled = (allTableCheckedCheckboxes > 0) ? false : true;
            }
        });
    });
}

const drawDataGrid = (id) => {
    const tableWrapper = $('<div class="overflow-auto max-h-[44rem]"></div>'); // Create a wrapper div for the table
    const table = $(`#${id}`).DataTable({
        ordering: true, // Need to make it work so it orders from the 1st row not the 2nd where the filters are
        order: [[0, 'asc']],
        // Make sure that the ordering is done on the 1st row not the 2nd where the filters are
        orderCellsTop: true,
        /*
        scrollY: 600,
        scrollX: 600,
        */
        //scrollCollapse: false,
        paging: true,
        pagingType: 'full_numbers',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        //stateSave: true,
        createdRow: function (row, data, dataIndex) {
            $(row).attr('tabindex', dataIndex)
            $(row).addClass('focus:outline-none focus:bg-gray-300 focus:text-gray-900 dark:focus:bg-gray-700 dark:focus:text-amber-500');
        },
        "columnDefs": [{
            "targets": "_all",
            "createdCell": function (td, cellData, rowData, row, col) {
                $(td).addClass('py-4 px-6 border border-slate-400 max-w-md break-words');
            }
        }],
        initComplete: function () {
            $(`#${id}-loading-table`).remove();
            document.getElementById(`${id}`).classList.remove('hidden');
        },
    });
    $(`#${id}`).wrap(tableWrapper); // Wrap the table with the wrapper div
    return table;
}


const drawDataGridFromData = (json, skeletonId) => {
    const tableWrapper = $('<div class="mx-2 overflow-auto max-h-[44rem]"></div>'); // Create a wrapper div for the table
    // Create the loading screen for the table
    const loadingScreen = tableLoadingScreen(skeletonId);
    console.log(loadingScreen);
    // Append the loading screen before the table
    $(`#${skeletonId}`).before(loadingScreen);

    const dataArray = [];
    const headers = [];
    const uniqueKeys = new Set();
    json.forEach(obj => {
        // Iterate over the keys and values of each user's data
        Object.keys(obj).forEach(key => {
            // Check if the key is not already present in the uniqueKeys set
            if (!uniqueKeys.has(key)) {
                // If not, push the key to the headers array and add it to the uniqueKeys set
                headers.push({ name: key });
                uniqueKeys.add(key);
            }
        });

        // Initialize an array to store user data
        const tempArray = Object.values(obj);

        // Push the tempArray to the dataArray
        dataArray.push(tempArray);
    });

    const tableHeaders = headers.map(column => ({
        title: column.name
        //data: column.name
    }));

    // Create the table and add data
    const table = $(`#${skeletonId}`).DataTable({
        ordering: true,
        data: dataArray,
        columns: tableHeaders,
        paging: true,
        pagingType: 'full_numbers',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
        createdRow: function (row, data, dataIndex) {
            $(row).attr('tabindex', dataIndex);
            $(row).addClass('focus:outline-none focus:bg-gray-300 focus:text-gray-900 dark:focus:bg-gray-700 dark:focus:text-amber-500');
        },
        // now let's apply a common set of classes to all <td>
        "columnDefs": [{
            "targets": "_all",
            "createdCell": function (td, cellData, rowData, row, col) {
                // Apply the common class names for each table cell
                $(td).addClass('py-4 px-6 border border-slate-400');
                // Let's deal with long text and truncate it so it doesn't make our table extremely long
                if (td.innerHTML.length > 60) {
                    $(td).addClass('truncate');
                    $(td).addClass('overflow-hidden');
                    //$(td).addClass('max-w-2xl');
                    // And here only show 60 cahracters of the text
                    $(td).text(decodeHTMLEntities(cellData).substring(0, 120) + '...');
                    // Add the full text as a title so it's visible on hover
                    $(td).attr('title', decodeHTMLEntities(cellData));
                } else {
                    $(td).text(decodeHTMLEntities(cellData));
                }
            }
        }],
        initComplete: function () {
            document.getElementById(loadingScreen.id).remove();
        }
    });

    // Create filter rows outside of header callback
    // First, get the first row of the thead
    $(`#${skeletonId} thead tr:first-child th`).append(' <span class="text-xs text-gray-400 cursor-pointer">&#x25B2;&#x25BC;</span>');
    const filtersRow = $('<tr></tr>').insertAfter($(`#${skeletonId} thead tr`));
    tableHeaders.forEach(header => {
        filtersRow.append(`<th class="py-4 px-6 border border-gray-400 max-w-md break-words"></th>`);
    });

    $(`#${skeletonId}`).wrap(tableWrapper); // Wrap the table with the wrapper div
    return table;
};

/**
 * Builds select filters for a DataTable.
 *
 * @param {DataTable} table - The DataTable instance.
 * @param {string} tableId - The ID of the table element.
 * @param {Array} columnSkipArray - An optional array of column indexes to skip.
 * @returns {void} - Creates the DataTable filters. Does not return a value.
 */
const buildDataGridFilters = (table, tableId, columnSkipArray = []) => {
    // Loop through each column of the DataTable
    table.columns().every(function (col) {
        if (columnSkipArray.includes(col)) {
            return;
        }
        const column = table.column(this, { search: 'applied' }); // Get the DataTable column object

        // Create a select element and append it to the appropriate table header cell. (1) in this case is the 2nd thead so it doesn't do it on the first where the column names are
        const select = $(`<select class="text-center m-1 p-1 text-sm text-gray-900 border border-${theme}-300 rounded bg-gray-50 focus:ring-${theme}-500 focus:border-${theme}-500 dark:bg-gray-700 dark:border-${theme}-600 dark:placeholder-gray-400 dark:text-white outline-none dark:focus:ring-${theme}-500 dark:focus:border-${theme}-500"><option value="">No filter</option></select>`)
            .appendTo($(`#${tableId} thead tr:eq(1) th`).eq(column.index()).empty())
            .on('change', function () {
                const val = $.fn.dataTable.util.escapeRegex(
                    $(this).val()
                );
                // Apply the selected filter value to the column and redraw the table
                column
                    .search(val ? '^' + val + '$' : '', true, false)
                    .draw();
            });

        // Calculate the maximum width for the select options
        let maxOptionWidth = Math.min(
            $(column.header()).outerWidth() || select.width(),
            150
        ); // You can adjust the maximum width as needed
        if (maxOptionWidth < 150) {
            maxOptionWidth = 150;
        }

        // Iterate through the unique values in the column, create options for the select element
        column.data().unique().sort().each(function (d, j) {
            if (d !== null) {
                let optionText = d;
                if (optionText.length > maxOptionWidth / 2)  {
                    if (optionText.length > maxOptionWidth / 2) {
                        // Truncate the option text if it's longer than the maxOptionWidth
                        optionText = optionText.substring(0, maxOptionWidth / 2) + '...';
                    } else {
                        optionText = optionText + '...';
                    }
                }
                // Append the option with the selected attribute if necessary
                select.append(`<option value="${d}" title="${d}">${optionText}</option>`);
            }
        });

        // Repopulate the select element based on the current search filter
        const currSearch = column.search();
        if (currSearch) {
            const searchValue = currSearch.substring(1, currSearch.length - 1);
            select.val(searchValue); // Set the selected value

            // Loop through the options and set the 'selected' attribute explicitly for the matched option
            select.find('option').each(function () {
                const optionValue = $(this).val();
                // We need to replace the special characters from searchValue as searchValue comes with escaped special characters from column.search(). We want to apply the selected prop for the currently selected option so it's visible what has been filtered right now
                if (optionValue === searchValue.replace(/\\/g, '')) {
                    $(this).prop('selected', true);
                } else {
                    $(this).prop('selected', false);
                }
            });

            select.addClass('border-red-500');
            select.addClass('dark:border-red-500');
        }

        $(`#${tableId} thead`).addClass('bg-gray-300 dark:bg-gray-600 sticky top-0 dark:text-gray-300 border-collapse');

        $(`#${tableId} thead tr:eq(0) th`).addClass('p-2');
        $(`#${tableId} thead tr:eq(0) th`).addClass('border');
        $(`#${tableId} thead tr:eq(0) th`).addClass('border-slate-400');
        
        $(`#${tableId} thead tr:eq(1) th`).addClass('p-4');
        $(`#${tableId} thead tr:eq(1) th`).addClass('border');
        $(`#${tableId} thead tr:eq(1) th`).addClass('border-slate-400');

    });
}

const createSkeletonTable = () => {
    // First decide the random ID
    const skeletonId = generateUniqueId(4);
    // Create the skeleton table
    const table = document.createElement('table');
    table.id = skeletonId;
    // Add some classes to the table
    table.classList.add('table-auto', 'mt-6', 'text-gray-700', 'dark:text-gray-400', 'border-collapse', 'border', 'border-slate-400', 'text-center');
    return table;
}

const tableLoadingScreen = (tableId) => {
    const loadingScreen = document.createElement('div');
    loadingScreen.id = `${tableId}-loading-table`;
    loadingScreen.classList.add('m-4', 'bg-gray-500', 'h-10', 'w-full', 'text-center', 'text-white');
    loadingScreen.innerHTML = `Data Loading... Please wait<svg class="inline mx-4 w-8 h-8 text-gray-200 dark:text-white animate-spin fill-blue-600 dark:fill-gray-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>`;
    return loadingScreen;
}