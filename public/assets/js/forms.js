// This is the fucntion that will create the modal for the form submission if confirm class on the form exists
const generateModal = (text, id) => {
    // Create the html element div
    const modalDiv = document.createElement('div');
    // The modal HTML
    const html = `<div id="${id}-mass-delete-modal" tabindex="-1" class="fixed inset-0 flex items-center justify-center z-50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto overflow-auto">
            <div class="relative p-4 max-w-md w-full mx-auto bg-white border border-gray-400 dark:border-gray-300 rounded-lg shadow dark:bg-gray-700">
                <button id="${id}-x-cancel" type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="${id}-modal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg aria-hidden="true" class="mx-auto mb-4 w-14 h-14 text-gray-400 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 id="${id}-modal-text" class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">${text}</h3>
                    <button id="${id}-submit" data-modal-toggle="${id}-modal" type="submit" class="my-2 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, I\'m sure
                    </button>
                    <button id="${id}-cancel" data-modal-toggle="${id}-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                </div>
            </div>
        </div>
    </div>`;
    // Append
    modalDiv.innerHTML += html;
    // Return
    return modalDiv;
}

// This function handles the form submission, takes care of the result div where data return is displayed
const handleFormFetch = (form, currentEvent, resultType) => {
    const resultDiv = form.nextSibling;
    if (resultDiv && resultDiv.classList.contains('generic-form-submit-div')) {
        resultDiv.remove();
    }
    let newResultDiv = document.createElement('div');
    newResultDiv.classList.add('ml-4', 'my-4', 'text-gray-900', 'dark:text-gray-300', 'generic-form-submit-div', 'break-words');
    form.parentNode.insertBefore(newResultDiv, form.nextSibling);
    //const initialClasses = currentEvent.submitter.className;
    const initialSubmitName = currentEvent.submitter.innerText;
    currentEvent.submitter.innerHTML = `
    <div role="status">
        <svg aria-hidden="false" class="inline w-6 h-6 text-gray-50 dark:text-white animate-spin fill-${theme}-600 dark:fill-${theme}-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
        </svg>
    </div>
    `;
    // Disable the submit button to prevent multiple submissions
    currentEvent.submitter.disabled = true;
    // Pick up the data from the form and urlencode is for POST
    const formData = new FormData(form);
    // If you still want to add specific values for checkboxes, you can do it like this
    const toggleCheckboxes = form.querySelectorAll('input[type="checkbox"]')
    if (toggleCheckboxes.length > 0) {
        toggleCheckboxes.forEach(checkbox => {
            // If the checkbox is checked, set its value to 1, otherwise, set it to 0
            let checkboxValue = checkbox.checked ? 1 : 0;
            formData.set(checkbox.name, checkboxValue);
            console.log(`Setting ${checkbox.name} to ${checkboxValue}`);
        });
    }
    const data = new URLSearchParams(formData);

    const formMethod = form.getAttribute('data-method');
    const csrfToken = form.querySelector('input[name="csrf_token"]').value;

    const fetchOptions = {
        method: formMethod,
        headers: {
            'secretheader': 'badass',
            'X-CSRF-TOKEN': csrfToken
        },
        redirect: 'manual'
    };

    if (formMethod === 'POST') {
        fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        fetchOptions.body = data;
    } else if (formMethod === 'PUT') {
        fetchOptions.headers['Content-Type'] = 'application/json';

        // Convert form data to a JavaScript object
        const formDataObject = {};
        formData.forEach((value, key) => {
            formDataObject[key] = value;
        });

        fetchOptions.body = JSON.stringify(formDataObject);
    } else if (formMethod !== 'GET' && formMethod !== 'DELETE') {
        fetchOptions.body = data;
    }

    // Fetch function
    fetch(form.action, fetchOptions)
        // Handle response
        .then(response => {
            currentEvent.submitter.disabled = false;
            const contentType = response.headers.get("content-type");
            // If response is redirect (0) or 403 return by the server, usually token expired, reload the page
            if (response.status === 0 || response.status === 403) {
                if (response.type === 'opaqueredirect') {
                    // redirect to the desired page response.url
                    location.reload(response.url);
                } else {
                    // Handle fetch interruption
                    newResultDiv.innerHTML = '<p class="font-semibold text-red-500">Fetch interrupted. Refreshing page</p>';
                    location.reload();
                }
            } else {
                if (contentType && contentType.indexOf("application/json") === -1) {
                    return response.text();
                }
                return response.json();
            }
            // Hanle after the response comes
        }).then(response => {
            currentEvent.submitter.innerText = initialSubmitName;
            newResultDiv.innerHTML = '';
            if (typeof response === 'undefined') {
                console.log(response);
                return;
            }
            // If the response is of type text()
            if (typeof response === 'string') {
                newResultDiv.innerHTML = `${response}`;
                // Let's search the response for table and if we find a table, get the table id
                const tablesArray = newResultDiv.querySelectorAll('table');
                // If there are any tables in the result, there is a good chance that they are datagrids and will need initialization
                if (tablesArray.length > 0) {
                    tablesArray.forEach(table => {
                        // get the table id
                        const tableId = table.getAttribute('id');
                        const dataTable = drawDataGrid(tableId);
                        buildDataGridFilters(dataTable, tableId, []);
                        // On every re-draw, rebuild them
                        dataTable.on('draw', () => {
                            console.log(`redraw occured`);
                            buildDataGridFilters(dataTable, tableId, []);
                        });
                    })
                }
                // If there were copy buttons in the response, let's initiate them
                copyToClipboard();
                // If we have html coming in, it could hold other forms, so let's initiate them. So let's find if there is a form.generic selector in the response
                initiateGenericForms();
                return;
            }
            // Return the button text to the initial state, remove the loading spinner
            // handle error from the front end api processing endpoint
            if (response.error) {
                newResultDiv.classList.add('text-red-500', 'font-semibold', 'ml-0');
                newResultDiv.innerHTML = `<p class="text-red-500 font-semibold ml-0">${JSON.stringify(response.error)}</p>`;
                return;
            }
            // handle error from the remote api processing endpoint
            if (response.result !== 'success') {
                // if response is [object Object] it means it's an error
                console.log(response.data);
                newResultDiv.classList.add('text-red-500', 'font-semibold', 'ml-0');
                newResultDiv.innerHTML = `<p class="text-red-500 font-semibold ml-0">${JSON.stringify(response.data)}</p>`;
            } else {
                // Now successful respones from the API
                // If data-reload is on the form, instruct to reload the page
                if (form.getAttribute("data-reload") === "true") {
                    location.reload();
                    // Otherwise display the returned data
                } else if (form.getAttribute("data-redirect")) {
                    location.href = form.getAttribute("data-redirect");
                    // If data-delete-current-row, delete the current <tr> element
                } else if (form.getAttribute("data-delete-current-row")) {
                    // Now find the closest tr and delete it
                    currentEvent.target.closest("tr").remove();
                    /*
                    let td = currentEvent.target.parentNode.parentNode;
                    let tr = td.parentNode;
                    tr.parentNode.removeChild(tr);
                    */
                } else {
                    console.log(`result type is ${typeof response.data}`)
                    const result = (typeof response.data === 'object') ? JSON.stringify(response.data) : response.data;
                    // If the result type is html, display the response as html
                    if (resultType === 'html') {
                        if (typeof response.data === 'object') {
                            newResultDiv.innerHTML = `<pre class="text-sm font-mono text-gray-800 dark:text-gray-400 whitespace-pre-wrap">${result}</pre>`;
                        } else {
                            newResultDiv.innerHTML = `<p class="font-semibold text-green-500 -ml-4">${result}</p>`;
                        }
                        return;
                    }
                    newResultDiv.innerText = result;
                }
            }
            // Catch errors
        }).catch(error => {
            if (error === null) {
                newResultDiv.innerHTML = `<p class="font-semibold text-red-500">An unknown error occured</p>`;
            } else {
                // Handle other errors, including network errors
                console.error('Error:', error);
                // Display the error message in newResultDiv
                newResultDiv.innerHTML = `<p class="font-semibold text-red-500">An error occurred: ${error.message}</p>`;
            }
        });
}

// This function handles form submission when confirm class exists
const handleFormSubmitWithConfirm = (currentEvent, form, initialSubmitName, resultType) => {
    // Create a random Id for the modal
    const randomId = generateUniqueId(4);
    // See if we are passing text for the modal via the form data-confirm
    const confirmText = form.getAttribute('data-confirm');
    // If we are set it, if not default to Are you sure?
    const modalText = (confirmText) ? form.getAttribute("data-confirm") : 'Are you sure?';
    // Generate the modal
    const modal = generateModal(modalText, randomId);
    // Insert the modal after the form
    form.parentNode.insertBefore(modal, form.nextSibling);
    // Let's get the buttons of the modal so we can attach event listeners
    const confirmButton = document.getElementById(`${randomId}-submit`);
    const cancelButton = document.getElementById(`${randomId}-cancel`);
    const xCancelButton = document.getElementById(`${randomId}-x-cancel`);

    if (form.classList.contains('double-confirm')) {
        // diable the submit button on the modal
        confirmButton.disabled = true;
        // create a new input field and add it to the modal
        const newInput = document.createElement("input");
        newInput.type = "text";
        newInput.classList.add('my-4', 'mx-auto', 'bg-gray-50', 'border', 'border-gray-300', 'outline-none', 'text-gray-900', 'text-sm', 'rounded-lg', 'focus:ring-red-500', 'focus:border-red-500', 'block', 'w-full', 'p-2.5', 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:placeholder-gray-400', 'dark:text-white', 'dark:focus:ring-red-500', 'dark:focus:border-red-500');
        confirmButton.parentNode.insertBefore(newInput, confirmButton);

        // Find out the keyword to type
        const keyWord = (form.getAttribute('data-double-confirm-keyword')) ?? null;

        const inputParagraph = document.createElement('p');

        inputParagraph.innerHTML = `Type <span class="break-all">"${keyWord}"</span> to continue`;

        newInput.parentNode.insertBefore(inputParagraph, newInput);

        confirmButton.classList.remove('bg-red-600', 'hover:bg-red-800');
        confirmButton.classList.add('bg-gray-400', 'hover:bg-gray-600');

        newInput.addEventListener('input', () => {
            if (newInput.value === keyWord) {
                confirmButton.classList.remove('bg-gray-400', 'hover:bg-gray-600');
                confirmButton.classList.add('bg-red-600', 'hover:bg-red-800');
                confirmButton.disabled = false;
            } else {
                confirmButton.classList.remove('bg-red-600', 'hover:bg-red-800');
                confirmButton.classList.add('bg-gray-400', 'hover:bg-gray-600');
                confirmButton.disabled = true;
            }
        })

    }
    // There are two close buttons so put them in an array
    const cancelButtonsArray = [cancelButton, xCancelButton];
    // Hide the modal
    modal.classList.remove('hidden');
    // So on Yes click on the modal, remove the modal and start the fetch function
    confirmButton.addEventListener('click', () => {
        modal.remove();
        handleFormFetch(form, currentEvent, resultType)
    });
    // If user cancels the modal, again remove the modal and return to the initial button text
    cancelButtonsArray.forEach(button => {
        button.addEventListener('click', () => {
            modal.remove();
            currentEvent.submitter.innerText = initialSubmitName;
        })
    })
}

/* Search within selects. For this to work the search input needs to be just before the select */
const searchInputs = document.querySelectorAll('input.filterSearch');

if (searchInputs.length > 0) {
    searchInputs.forEach(input => {
        input.addEventListener('input', () => {
            const searchValue = input.value.toLowerCase();
            const select = input.parentNode.querySelector('select');
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                if (option.value.toLowerCase().indexOf(searchValue) > -1) {
                    option.style.display = '';
                    // Also make sure that the option is selected
                    option.selected = true;
                } else {
                    option.style.display = 'none';
                }
            })
        })
    })
}


/* Generic forms submit functionality */
const initiateGenericForms = () => {
    // Query all forms produced by the backend
    const genericForms = document.querySelectorAll('form.generic-form');

    // If there are any forms
    if (genericForms.length > 0) {
        console.log(`Initializing ${genericForms.length} generic forms`);
        // Loop through each
        genericForms.forEach(form => {
            // Let's search for checkbox groups in the form. They all start with checkbox-group- followed by a random string. Let's try to catch each group
            const checkboxesInGroups = form.querySelectorAll('[class*="checkbox-group-"]');
            console.log(`Found ${checkboxesInGroups.length} checkboxes in groups in form`);

            checkboxesInGroups.forEach(checkbox => {
                const groupName = checkbox.classList[checkbox.classList.length - 1];
                console.log(`Group name is ${groupName}`);

                const checkboxesPerGroup = form.querySelectorAll(`.${groupName}`);
                console.log(`Found ${checkboxesPerGroup.length} checkboxes in group ${groupName}`);

                checkboxesPerGroup.forEach(groupCheckbox => {
                    groupCheckbox.addEventListener('change', () => {
                        // Uncheck other checkboxes in the same group
                        checkboxesPerGroup.forEach(otherCheckbox => {
                            if (otherCheckbox !== groupCheckbox && otherCheckbox.type === 'checkbox') {
                                otherCheckbox.checked = false;
                            }
                        });
                    });
                });
            });

            // Check if the event listener is already attached
            if (!form.hasAttribute('data-submit-listener')) {
                // Attach submit event
                form.addEventListener('submit', (event) => {
                    // Prevent normal submitting
                    event.preventDefault();
                    // Check the result type (text or html) if declared in the form
                    let resultType = form.getAttribute('data-result');
                    // Remember the initial button text
                    const initialButtonText = event.submitter.innerText;
                    // If there is a confirm required (via confirm class)
                    if (form.classList.contains('confirm')) {
                        // use the handle with confirm function
                        handleFormSubmitWithConfirm(event, form, initialButtonText, resultType);
                    } else {
                        // Otherwise just proceed with the fetch
                        handleFormFetch(form, event, resultType);
                    }
                });

                // Mark the form to indicate that the listener is attached
                form.setAttribute('data-submit-listener', 'true');
            }
        });
    }
}

initiateGenericForms();

const changeForms = document.querySelectorAll('form.select-submitter');

if (changeForms.length > 0) {
    changeForms.forEach(form => {
        const currentSelectionIndex = form.firstChild.selectedIndex;
        const formData = new FormData(form);
        const apiAction = formData.get('api-action');
        const organization = formData.get('organization');
        const username = formData.get('username');
        form.addEventListener('change', (event) => {
            const currentSelectedOption = form.firstChild.options[form.firstChild.selectedIndex].value;
            event.preventDefault();
            let modalText = 'Are you sure?';
            // Let's find out if we deal with update membership
            if (apiAction) {
                if (apiAction === 'update-membership') {
                    if (currentSelectedOption === 'Owner') {
                        modalText = `You are choosing to make ${username} an Owner of ${organization} organization. This will convert your role to Contributor and ${username}\'s to Owner. Are you sure you want to proceed?`;
                    } else {
                        modalText = `You are changing the role of ${username} to ${currentSelectedOption} in ${organization}. Are you sure you want to proceed?`;
                    }
                }
            }
            const randomId = generateUniqueId(4);
            // Generate the modal
            const modal = generateModal(modalText, randomId);
            // Insert the modal after the form
            form.parentNode.insertBefore(modal, form.nextSibling);
            // Let's get the buttons of the modal so we can attach event listeners
            const confirmButton = document.getElementById(`${randomId}-submit`);
            const cancelButton = document.getElementById(`${randomId}-cancel`);
            const xCancelButton = document.getElementById(`${randomId}-x-cancel`);
            // There are two close buttons so put them in an array
            const cancelButtonsArray = [cancelButton, xCancelButton];
            // Hide the modal
            modal.classList.remove('hidden');
            // So on Yes click on the modal, remove the modal and start the fetch function
            confirmButton.addEventListener('click', () => {
                modal.remove();
                let loadingDiv = document.createElement('div');
                loadingDiv.classList.add('ml-2');
                loadingDiv.innerHTML = `
                    <div role="status">
                    <svg aria-hidden="false" class="inline mr-2 w-6 h-6 text-gray-200 dark:text-white animate-spin fill-blue-600 dark:fill-amber-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    </div>
                    `;
                form.parentNode.insertBefore(loadingDiv, form.nextSibling);
                modal.remove();

                fetch(form.action, {
                    method: form.method,
                    body: new URLSearchParams(new FormData(form)),
                    redirect: 'manual'
                }).then(response => response.json()
                ).then(json => {
                    console.log(json);
                    if (json.error) {
                        alert(json.error);
                    } else {
                        loadingDiv.classList.add('text-green-500', 'font-semibold', 'text-xl');
                        loadingDiv.innerHTML = '&#x2713;';
                    }
                })
            });
            // If user cancels the modal, again remove the modal and return to the initial button text
            cancelButtonsArray.forEach(button => {
                button.addEventListener('click', () => {
                    modal.remove();
                    form.firstChild.selectedIndex = currentSelectionIndex;
                })
            })
        })
    })
}