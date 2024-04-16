/* Copy to Clipboard functionality. Just add the class "c0py" to the element you want to add a button to copy. */
const copyToClipboard = () => {
    // Find all elements with class "c0py"
    const elements = document.getElementsByClassName("c0py");

    const theme = document.querySelector('input[type="hidden"][name="theme"]').value;
    // Loop if data-processed is true
    // Iterate over the found elements
    Array.from(elements).forEach(element => {
        // Only proceed if data-hasCopyToClipboard is set
        if (!element.dataset.hasCopyToClipboard) {
            // Apply it to the element so we don't process it again
            element.dataset.hasCopyToClipboard = true;
            // Create the copy button
            const button = document.createElement("button");
            button.classList.add('inline-flex', 'items-center');
            // Add a data attribute that will tell us that we've already processed this element
            button.setAttribute('data-processed', 'true');
            // Set button content as the provided SVG
            button.innerHTML = `<svg class="w-6 h-6 text-${theme}-500 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="2" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
            </svg>`;

            // Set the tooltip text
            button.title = "Copy to clipboard";

            // Create the tooltip div element
            const div1 = document.createElement('div');
            const uniqueId = `tooltip-${generateUniqueId(8)}`;
            div1.setAttribute('id', uniqueId);
            div1.setAttribute('role', 'tooltip');
            div1.classList.add('absolute', 'hidden', `bg-${theme}-500`, 'text-gray-50', 'p-1', 'text-sm');

            // Create a container element
            const container = document.createElement('div');
            container.classList.add('copy-container', 'inline-block');

            // Append the new div elements to the container
            container.appendChild(button);
            container.appendChild(div1);

            // Append the container after the element
            element.insertAdjacentElement('afterend', container);

            // Set the click event handler for copying to clipboard
            button.addEventListener("click", () => {
                const textToCopy = element.textContent || element.innerText;
                navigator.clipboard.writeText(textToCopy)
                    .then(() => {
                        div1.textContent = 'Copied';
                        console.log("Text copied to clipboard:", textToCopy);

                        // Change the color to red
                        button.querySelector("svg").classList.add("text-red-500");
                    })
                    .catch(error => {
                        div1.textContent = 'Failed to copy';
                        console.error("Failed to copy text:", error);
                    });

                // Toggle the visibility of the tooltip
                div1.classList.toggle('hidden');

                // Hide the tooltip after 2 seconds
                setTimeout(() => {
                    div1.classList.add('hidden');
                }, 1000);
            });

            // Position the tooltip relative to the button
            button.addEventListener("mouseenter", () => {
                const buttonRect = button.getBoundingClientRect();
                const buttonCenterX = buttonRect.left + buttonRect.width / 1;
                const buttonTop = buttonRect.top;
                const tooltipHeight = div1.offsetHeight;

                div1.style.left = `${buttonCenterX}px`;
                div1.style.top = `${buttonTop - tooltipHeight}px`;
            });
        }
    });
};

// Call the function when the DOM content is loaded
document.addEventListener("DOMContentLoaded", copyToClipboard);