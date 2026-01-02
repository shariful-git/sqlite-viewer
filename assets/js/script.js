const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('fileInput');
const form = document.getElementById('uploadForm');
const defaultState = document.getElementById('defaultState');
const fileSelectedState = document.getElementById('fileSelectedState');
const fileNameDisplay = document.getElementById('fileNameDisplay');

// Prevent defaults
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
    dropArea.addEventListener(event, e => {
        e.preventDefault();
        e.stopPropagation();
    });
});

// Highlight on drag
['dragenter', 'dragover'].forEach(event => {
    dropArea.addEventListener(event, () => dropArea.classList.add('dragover'));
});

['dragleave', 'drop'].forEach(event => {
    dropArea.addEventListener(event, () => dropArea.classList.remove('dragover'));
});

// Handle drop
dropArea.addEventListener('drop', e => {
    const files = e.dataTransfer.files;
    if (files.length) handleFiles(files);
});

// Handle file selection
fileInput.addEventListener('change', () => {
    if (fileInput.files.length) handleFiles(fileInput.files);
});

// Click to open
dropArea.addEventListener('click', () => fileInput.click());

function handleFiles(files) {
    const file = files[0];
    if (file) {
        fileNameDisplay.textContent = file.name;
        defaultState.classList.add('d-none');
        fileSelectedState.classList.remove('d-none');
        form.submit(); // Auto-submit on select/drop
    }
}