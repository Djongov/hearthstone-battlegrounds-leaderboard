/* Dark/Light Theme Changes */
const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
const themeToggleBtn = document.getElementById('theme-toggle');

// Function to set theme based on localStorage
function setThemeFromLocalStorage() {
    if (localStorage.getItem('color-theme') === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Function to set button state based on localStorage
function setButtonStateFromLocalStorage() {
    if (localStorage.getItem('color-theme') === 'dark') {
        themeToggleDarkIcon.classList.add('hidden');
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
        themeToggleLightIcon.classList.add('hidden');
    }
}

// Check if the user's preferred color scheme is dark
const preferredColorScheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && preferredColorScheme)) {
    setButtonStateFromLocalStorage();
} else {
    themeToggleDarkIcon.classList.remove('hidden');
}

// Auto set class and button state based on the local storage theme
if (localStorage.getItem('color-theme')) {
    setThemeFromLocalStorage();
    setButtonStateFromLocalStorage();
} else {
    if (preferredColorScheme) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    } else {
        localStorage.setItem('color-theme', 'light');
    }
}

// Event listener for theme toggle button
if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {
        // Toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // Toggle theme in current tab
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }

        // Set button state in current tab
        setButtonStateFromLocalStorage();
    });
}

// Event listener for storage change in other tabs/windows
window.addEventListener('storage', function (event) {
    if (event.key === 'color-theme') {
        setThemeFromLocalStorage();
        setButtonStateFromLocalStorage();
    }
});

// I want to set a constant called 'theme' that will be used across the script, its value needs to be taken from 'input[type="hidden"][name="theme"]' if there such an elememt, if not it needs to be 'sky'

// Initiate theme across the script
const themeInput = document.querySelector('input[type="hidden"][name="theme"]');
const theme = themeInput ? themeInput.value : 'sky';

/* Back Button */
const backButtons = document.querySelectorAll('.back-button');

if (backButtons.length > 0) {
    backButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (history.length > 1) {
                history.back();
            } else {
                location.href = '/'
            }
        }, false)
    });
}

const generateUniqueId = (length) => {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let id = '';

    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * charactersLength);
        id += characters.charAt(randomIndex);
    }

    return id;
};

const isJSONString = (string) => {
    try {
        JSON.parse(string);
        return true;
    } catch (error) {
        return false
    }
}

const decodeHTMLEntities = (text) => {
    var element = document.createElement('textarea');
    element.innerHTML = text;
    return element.value;
}

const escapeHtml = (input) => {
    return String(input).replace(/[&<>"'\/]/g, function (s) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;'
        }[s];
    });
}

/* Scroll to top button */

// Actual snooth scroll to top function. /8 shows how smooth or quick it should do it
const scrollToTop = () => {
    const c = document.documentElement.scrollTop || document.body.scrollTop;
    if (c > 0) {
        window.requestAnimationFrame(scrollToTop);
        window.scrollTo(0, c - c / 8);
    }
};

// Scroll to top button and function
const backToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

//Get the button
let scrollToTopButton = document.getElementById("btn-back-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

const scrollFunction = () => {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopButton.style.display = "block";
    } else {
        scrollToTopButton.style.display = "none";
    }
}
// When the user clicks on the button, scroll to the top of the document
scrollToTopButton.addEventListener("click", backToTop);




/* Autload stuff */

const autoLoadParams = document.querySelectorAll('input[type="hidden"][name="autoload"]');

if (autoLoadParams.length > 0) {
    autoLoadParams.forEach(input => {
        const value = JSON.parse(input.value);
        const type = value.type;
        const data = value.data;
        if (type === 'doughnut') {
            gauge(data.title, data.parentDiv, data.width, data.height, data.labels, data.data);
        }
        if (type === 'piechart') {
            createPieChart(data.title, data.parentDiv, data.width, data.title, data.height, data.labels, data.data);
        }
        if (type === 'linechart') {
            createLineChart(data.title, data.parentDiv, data.width, data.height, data.labels, data.datasets);
        }
        if (type === 'barchart') {
            createBarChart(data.title, data.parentDiv, data.width, data.height, data.labels, data.data);
        }
        if (type === 'table') {
            // Create a table element
            const table = createSkeletonTable();
            // Add to parentDiv
            document.getElementById(value.parentDiv).appendChild(table);
            // Create the DataGrid table object
            const dataGridTable = drawDataGridFromData(data, table.id);
            // And the filters
            buildDataGridFilters(dataGridTable, table.id);
        }
    })
}

// Handle the submitting of the IP address form if there is a get request with query paramter "ip". If it does, find a form with .generic-form class and simulate a click on the form submitter
const urlParams = new URLSearchParams(window.location.search);
const ip = urlParams.get('ip');
if (ip) {
    const form = document.querySelector('form.generic-form');
    // Also update the value of input with name "ip" to the ip address
    const ipInput = document.querySelector('input[name="ipAddress"]');
    ipInput.value = ip;

    // Find the submit button by tag name (button) or by class name (e.g., "submit-button-class")
    const submitButton = form.querySelector('button'); // By tag name
    // Or, if the button has a class name:
    // const submitButton = form.querySelector('.submit-button-class');
    if (submitButton) {
        submitButton.click();
    }
}

// Universal fetch function
const fetchData = async (url, method, jsonData) => {
    console.log(jsonData);
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonData),
            redirect: 'manual'
        });

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json(); // If the response is JSON, parse and return it
        } else {
            return await response.text(); // If not JSON, return the response as text
        }
    } catch (error) {
        throw new Error(`Fetch error: ${error.message}`);
    }
}

const createLoader = (parentDiv, id, text) => {
    const loaderDiv = document.createElement('div');
    loaderDiv.id = id;
    loaderDiv.classList.add('text-left', 'hidden', 'mt-6');
    const loader = document.createElement('div');
    loader.role = 'status';
    loaderDiv.appendChild(loader);
    const svg = document.createElement('svg');
    svg.setAttribute('aria-hidden', 'true');
    svg.classList.add('inline', 'mr-2', 'w-8', 'h-8', 'text-gray-200', 'dark:text-white', 'animate-spin', 'fill-blue-500', 'dark:fill-amber-500');
    svg.setAttribute('viewBox', '0 0 100 101');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    const path = document.createElement('path');
    path.setAttribute('d', 'M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"');
    svg.appendChild(path);
    loader.appendChild(svg);
    const span = document.createElement('span');
    span.textContent = text;
    loader.appendChild(span);
    parentDiv.appendChild(loaderDiv);
}

const loaderString = () => {
    return `
    <div role="status" class="flex items-center justify-center">
        <span class="sr-only">Loading...</span>
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0
                    014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    </div>
    `;
}

const editModal = (id) => {
    let html = `
        <!-- Main modal -->
        <div id="${id}-container" class="mx-2 relative w-full bg-gray-50 dark:bg-gray-700 max-w-2xl max-h-full mx-auto border border-gray-700 dark:border-gray-400 shadow overflow-auto">
            <!-- Modal content -->
            <div class="relative overflow-auto max-h-[44rem] rounded-lg dark:bg-gray-700 ">
                <!-- Modal header -->
                <div class="mx-4 flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit
                    </h3>
                    <button id="${id}-x-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="${id}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div id="${id}-body" class="p-4 md:p-5 space-y-4 break-words">
                    <p class="text-base leading-relaxed text-gray-700 dark:text-gray-400">
                        Loading data...
                    </p>
                </div>
            </div>
            <!-- Modal Result -->
            <div id="${id}-result" class="m-4"></div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button id="${id}-edit" data-modal-hide="${id}" type="button" class="text-white bg-${theme}-700 hover:bg-${theme}-800 focus:ring-4 focus:outline-none focus:ring-${theme}-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-${theme}-600 dark:hover:bg-${theme}-700 dark:focus:ring-${theme}-800">Edit</button>
                <button id="${id}-close-button" data-modal-hide="${id}" type="button" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-${theme}-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
            </div>
        </div>
    `;
    const modal = document.createElement('div');
    modal.id = id;
    // Add data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
    modal.classList.add('hidden', 'overflow-auto', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full', 'mt-8');
    modal.setAttribute('data-modal-backdrop', 'static');
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');
    // Add the html to the modal
    modal.innerHTML = html;
    return modal;
}

const deleteModal = (id, data) => {
    const jsonData = JSON.stringify(data);
    let html = `
    <!-- Main modal -->
        <div id="${id}-container" class="relative w-full max-w-2xl max-h-full mx-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 border border-gray-700 dark:border-gray-400">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Delete 
                    </h3>
                    <button id="${id}-x-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="${id}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div id="${id}-body" class="p-4 md:p-5 space-y-4 break-words">
                    <p class="text-base leading-relaxed text-gray-700 dark:text-gray-400">
                        Are you sure you want to delete entry with id: ${jsonData}?
                    </p>
                </div>
                <!-- Modal Result -->
                <div id="${id}-result" class="m-4"></div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button id="${id}-delete" data-modal-hide="${id}" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Delete</button>
                    <button id="${id}-close-button" data-modal-hide="${id}" type="button" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
                </div>
            </div>
        </div>
    `;
    const modal = document.createElement('div');
    modal.id = id;
    // Add data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
    modal.classList.add('hidden', 'overflow-y-hidden', 'overflow-x-hidden', 'fixed', 'top-0', 'right-0', 'left-0', 'z-50', 'justify-center', 'items-center', 'w-full', 'md:inset-0', 'h-[calc(100%-1rem)]', 'max-h-full', 'mt-12');
    modal.setAttribute('data-modal-backdrop', 'static');
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');
    // Add the html to the modal
    modal.innerHTML = html;
    return modal;
}

const toggleBlur = (excludeElement) => {
    const addBlurClass = (element) => {
        // Apply the hardcoded class 'blur-sm' to the element
        element.classList.toggle('blur-sm');

        // Recursively process child elements
        for (const child of element.children) {
            addBlurClass(child);
        }
    };

    // Get all elements in the body
    const allElements = document.body.getElementsByTagName('*');

    // Iterate through the elements and apply the class conditionally
    for (const currentElement of allElements) {
        // Check if the element is not the one to exclude or its descendant
        if (currentElement !== excludeElement && !excludeElement.contains(currentElement)) {
            addBlurClass(currentElement);
        }
    }
}