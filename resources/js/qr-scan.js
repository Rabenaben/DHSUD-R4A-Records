import { Html5Qrcode } from 'html5-qrcode';

function parseQrPayload(text) {
    if (!text) return null;

    // Allow direct URL payloads
    if (text.startsWith('http://') || text.startsWith('https://')) {
        try {
            const url = new URL(text);
            if (url.pathname.startsWith('/qr/info/') || url.pathname.startsWith('/qr/open/')) {
                return { url: url.pathname + url.search };
            }
        } catch (error) {
            // ignore URL parsing errors
        }
    }

    // Try JSON payload { type, docket_no }
    try {
        const data = JSON.parse(text);
        if (data && data.type && data.docket_no) {
            return {
                type: String(data.type).toLowerCase(),
                docketNo: String(data.docket_no),
            };
        }
    } catch (error) {
        // ignore JSON parsing errors
    }

    return null;
}

function initQrScanner() {
    const readerEl = document.getElementById('qr-reader');
    if (!readerEl) return;

    const statusEl = document.getElementById('qr-status');
    const usbStatusEl = document.getElementById('usb-status');
    const usbForm = document.getElementById('usb-scan-form');
    const usbInput = document.getElementById('usb-scan-input');
    const usbFocusBtn = document.getElementById('usb-scan-focus');
    const scanModeSelect = document.getElementById('scan-mode');
    const cameraSection = document.getElementById('camera-scan-section');
    const usbSection = document.getElementById('usb-scan-section');
    const startBtn = document.getElementById('qr-start-btn');
    const stopBtn = document.getElementById('qr-stop-btn');
    const qrCode = new Html5Qrcode('qr-reader');
    let isRunning = false;
    let usbScanTimeout = null;
    let usbRedirected = false;

    const setStatus = (message) => {
        if (statusEl) statusEl.textContent = message;
    };

    const onSuccess = async (decodedText) => {
        const payload = parseQrPayload(decodedText);
        if (!payload) {
            setStatus('Invalid QR code. Please try again.');
            if (usbStatusEl) usbStatusEl.textContent = 'Invalid QR code. Please try again.';
            return;
        }

        setStatus('QR detected. Redirecting...');
        if (usbStatusEl) usbStatusEl.textContent = 'QR detected. Redirecting...';
        if (isRunning) {
            isRunning = false;
            await qrCode.stop();
        }

        if (payload.url) {
            window.location.href = payload.url;
            return;
        }

        const url = `/qr/info/${encodeURIComponent(payload.type)}/${encodeURIComponent(payload.docketNo)}`;
        window.location.href = url;
    };

    const onFailure = () => {
        // Do nothing; continuous scanning
    };

    const startScanner = async () => {
        if (isRunning) return;
        try {
            await qrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                onSuccess,
                onFailure
            );
            isRunning = true;
            setStatus('Camera is running. Point to a QR code.');
        } catch (error) {
            console.error(error);
            setStatus('Unable to start camera. Please allow camera access.');
        }
    };

    const stopScanner = async () => {
        if (!isRunning) return;
        try {
            await qrCode.stop();
            isRunning = false;
            setStatus('Camera is idle.');
        } catch (error) {
            console.error(error);
            setStatus('Unable to stop camera.');
        }
    };

    const setMode = async (mode) => {
        if (mode === 'usb') {
            cameraSection?.classList.add('hidden');
            usbSection?.classList.remove('hidden');
            await stopScanner();
            usbRedirected = false;
            usbInput?.focus();
            if (usbStatusEl) usbStatusEl.textContent = 'Ready for USB scan.';
            return;
        }

        usbSection?.classList.add('hidden');
        cameraSection?.classList.remove('hidden');
        await startScanner();
    };

    startBtn?.addEventListener('click', () => setMode('camera'));
    stopBtn?.addEventListener('click', stopScanner);

    usbForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        const value = usbInput?.value?.trim() || '';
        if (!value) {
            if (usbStatusEl) usbStatusEl.textContent = 'Please scan a QR code first.';
            return;
        }
        if (usbRedirected) return;
        const payload = parseQrPayload(value);
        if (!payload) {
            if (usbStatusEl) usbStatusEl.textContent = 'Invalid QR code payload. Please scan again.';
            return;
        }
        if (usbStatusEl) usbStatusEl.textContent = 'QR detected. Redirecting...';
        usbRedirected = true;

        if (payload.url) {
            window.location.href = payload.url;
            return;
        }

        const url = `/qr/info/${encodeURIComponent(payload.type)}/${encodeURIComponent(payload.docketNo)}`;
        window.location.href = url;
    });

    usbFocusBtn?.addEventListener('click', () => {
        usbInput?.focus();
        if (usbStatusEl) usbStatusEl.textContent = 'Ready for USB scan.';
    });

    usbInput?.addEventListener('input', () => {
        if (usbStatusEl) usbStatusEl.textContent = 'Receiving scan input...';
        if (usbScanTimeout) {
            clearTimeout(usbScanTimeout);
        }
        usbScanTimeout = setTimeout(() => {
            const value = usbInput?.value?.trim() || '';
            if (!value || usbRedirected) return;
            const payload = parseQrPayload(value);
            if (!payload) return;
            if (usbStatusEl) usbStatusEl.textContent = 'QR detected. Redirecting...';
            usbRedirected = true;
            if (payload.url) {
                window.location.href = payload.url;
                return;
            }
            const url = `/qr/info/${encodeURIComponent(payload.type)}/${encodeURIComponent(payload.docketNo)}`;
            window.location.href = url;
        }, 300);
    });

    usbInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            usbForm?.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    scanModeSelect?.addEventListener('change', (event) => {
        const mode = event.target.value;
        setMode(mode);
    });

    // Auto-start with camera mode
    setMode(scanModeSelect?.value || 'camera');
}

document.addEventListener('DOMContentLoaded', initQrScanner);
