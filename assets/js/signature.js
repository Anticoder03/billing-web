// Signature pad initialization and handling
let companySignaturePad, clientSignaturePad;

function initializeSignaturePads() {
    const companyCanvas = document.getElementById('company-signature-pad');
    const clientCanvas = document.getElementById('client-signature-pad');

    if (companyCanvas) {
        companySignaturePad = new SignaturePad(companyCanvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Resize canvas
        resizeCanvas(companyCanvas);
    }

    if (clientCanvas) {
        clientSignaturePad = new SignaturePad(clientCanvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Resize canvas
        resizeCanvas(clientCanvas);
    }
}

function resizeCanvas(canvas) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
}

function clearCompanySignature() {
    if (companySignaturePad) {
        companySignaturePad.clear();
    }
}

function clearClientSignature() {
    if (clientSignaturePad) {
        clientSignaturePad.clear();
    }
}

function getCompanySignature() {
    if (companySignaturePad && !companySignaturePad.isEmpty()) {
        return companySignaturePad.toDataURL();
    }
    return null;
}

function getClientSignature() {
    if (clientSignaturePad && !clientSignaturePad.isEmpty()) {
        return clientSignaturePad.toDataURL();
    }
    return null;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('company-signature-pad') || document.getElementById('client-signature-pad')) {
        initializeSignaturePads();
    }
});
