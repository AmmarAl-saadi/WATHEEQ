/**
 * Watheeq ERP — Seamless Hardware & Camera Barcode / QR Scanner Engine
 * Designed for ultra-fast, high-precision retail & warehouse operations.
 */
window.WatheeqScanner = (function () {
    let audioCtx = null;
    let globalBuffer = '';
    let lastKeyTime = 0;
    let firstKeyTime = 0;
    const scannerTimingThreshold = 60; // ms between keystrokes for hardware wedge scanners
    let videoStream = null;
    let cameraDetector = null;

    // Web Audio Synthesizer (Zero Audio Files Needed)
    function playBeep(type = 'success') {
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            const now = audioCtx.currentTime;

            if (type === 'success') {
                // High-pitch crisp retail scanner chirp
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, now); // A5
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);
                osc.start(now);
                osc.stop(now + 0.08);
            } else if (type === 'increment') {
                // Secondary chirp when item quantity is incremented
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1046.5, now); // C6
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.07);
                osc.start(now);
                osc.stop(now + 0.07);
            } else if (type === 'error') {
                // Low error buzz
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, now); // A3
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.18);
                osc.start(now);
                osc.stop(now + 0.18);
            }
        } catch (e) {
            console.warn('AudioContext not available:', e);
        }
    }

    // Floating Visual Toast Notification
    function showToast(message, isSuccess = true) {
        let toast = document.getElementById('watheeqScannerToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'watheeqScannerToast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 99999;
                padding: 10px 20px;
                border-radius: 12px;
                font-size: 13px;
                font-weight: 700;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                transition: opacity 0.3s ease, transform 0.3s ease;
                pointer-events: none;
                font-family: inherit;
                direction: rtl;
            `;
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        if (isSuccess) {
            toast.style.background = '#1F2E4A';
            toast.style.color = '#FFFFFF';
            toast.style.border = '1px solid rgba(255,255,255,0.2)';
        } else {
            toast.style.background = '#BE123C';
            toast.style.color = '#FFFFFF';
            toast.style.border = '1px solid rgba(255,255,255,0.2)';
        }

        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';

        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(-10px)';
        }, 2200);
    }

    // Fast API Lookup
    async function lookupProduct(code) {
        try {
            const res = await fetch('/api/products/lookup?q=' + encodeURIComponent(code));
            const data = await res.json();
            return data;
        } catch (e) {
            console.error('Barcode lookup error:', e);
            return { found: false, message: 'خطأ في الاتصال بالخادم' };
        }
    }

    // Attach Hardware Wedge Sniffer
    function initHardwareScanner(callback, inputElementId = 'barcodeInput') {
        const input = inputElementId ? document.getElementById(inputElementId) : null;

        // Auto-focus input on load
        if (input) {
            input.focus();
            // Re-focus input on background clicks to keep scanner ready
            document.addEventListener('click', function (e) {
                if (!['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A'].includes(e.target.tagName)) {
                    input.focus();
                }
            });
        }

        // Direct Input Event (When typing or scanned into the box)
        if (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = input.value.trim();
                    if (code) {
                        input.value = '';
                        triggerScan(code, callback);
                    }
                }
            });
        }

        // Global Sniffer (Catches scanner even when focused elsewhere!)
        window.addEventListener('keydown', function (e) {
            const now = Date.now();
            const timeDiff = now - lastKeyTime;
            lastKeyTime = now;

            const activeTag = document.activeElement ? document.activeElement.tagName : '';
            const isInsideOtherInput = (activeTag === 'INPUT' || activeTag === 'TEXTAREA') && document.activeElement !== input;

            if (e.key === 'Enter') {
                if (globalBuffer.length >= 2) {
                    const elapsed = now - firstKeyTime;
                    const avgTime = elapsed / globalBuffer.length;

                    // If inside another input, only treat as scanner if keys arrived in rapid burst (<65ms avg)
                    if (isInsideOtherInput && avgTime > 65) {
                        globalBuffer = '';
                        return; // Human was typing in a textarea/input and hit Enter
                    }

                    e.preventDefault();
                    const scannedCode = globalBuffer.trim();
                    globalBuffer = '';
                    if (input) input.value = '';

                    // If scanner filled into an unintended input, remove the scanned text from it
                    if (isInsideOtherInput && document.activeElement && document.activeElement.value) {
                        if (document.activeElement.value.endsWith(scannedCode)) {
                            document.activeElement.value = document.activeElement.value.slice(0, -scannedCode.length).trim();
                        }
                    }

                    triggerScan(scannedCode, callback);
                    return;
                }
                globalBuffer = '';
            } else if (e.key.length === 1) {
                if (timeDiff < scannerTimingThreshold) {
                    globalBuffer += e.key;
                } else {
                    // Reset buffer if human typing slowly
                    globalBuffer = e.key;
                    firstKeyTime = now;
                }
            }
        });
    }

    // Trigger Scan Action
    async function triggerScan(rawCode, callback) {
        if (!rawCode) return;

        // Clean non-printable characters (e.g. STX, ETX, CR/LF sent by some barcode readers)
        let code = String(rawCode).replace(/[\x00-\x1F\x7F]/g, '').trim();
        if (!code) return;

        // Extract barcode/code from JSON if QR has JSON payload
        let cleanCode = code;
        if ((code.startsWith('{') && code.endsWith('}')) || (code.startsWith('[') && code.endsWith(']'))) {
            try {
                const parsed = JSON.parse(code);
                cleanCode = parsed.barcode || parsed.code || parsed.sku || parsed.id || code;
            } catch (err) {}
        } else if (code.includes('://')) {
            // URL payload: extract query param ?code=... or last path segment
            try {
                const url = new URL(code);
                cleanCode = url.searchParams.get('code') || url.searchParams.get('barcode') || url.pathname.split('/').filter(Boolean).pop() || code;
            } catch (err) {}
        } else if (code.includes(':')) {
            // Key-value pair like "sku:ITM-001" or "code:ITM-001"
            const parts = code.split(':');
            if (parts.length === 2 && ['sku', 'code', 'barcode', 'item'].includes(parts[0].toLowerCase().trim())) {
                cleanCode = parts[1].trim();
            }
        }

        const result = await lookupProduct(cleanCode);
        if (result && result.found) {
            callback(result.product, 'new');
        } else {
            playBeep('error');
            showToast('لم يتم العثور على صنف بالرمز: ' + cleanCode, false);
        }
    }

    let activeHtml5QrCode = null;

    // Camera Barcode / QR Scanner using Html5Qrcode (Zero alert, 100% offline, cross-browser)
    async function startCameraScanner(elementId, onDetected) {
        let targetEl = document.getElementById(elementId);
        if (!targetEl) return;

        // If target is a <video> element, swap it to a <div> container for Html5Qrcode
        if (targetEl.tagName === 'VIDEO') {
            const div = document.createElement('div');
            div.id = elementId;
            div.className = 'w-full h-full min-h-[220px] bg-black rounded-xl overflow-hidden flex items-center justify-center';
            targetEl.parentNode.replaceChild(div, targetEl);
            targetEl = div;
        }

        if (activeHtml5QrCode) {
            try {
                await activeHtml5QrCode.stop();
                activeHtml5QrCode.clear();
            } catch (e) {}
            activeHtml5QrCode = null;
        }

        if (typeof Html5Qrcode !== 'undefined') {
            try {
                activeHtml5QrCode = new Html5Qrcode(elementId);
                const config = {
                    fps: 15,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        return { width: Math.max(Math.floor(minEdge * 0.75), 180), height: Math.max(Math.floor(minEdge * 0.75), 180) };
                    },
                    aspectRatio: 1.333
                };

                const onScanSuccess = (decodedText) => {
                    stopCameraScanner(elementId);
                    triggerScan(decodedText, onDetected);
                };

                // Try back camera first
                activeHtml5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    () => {}
                ).catch(err => {
                    console.warn("Back camera facingMode failed, listing all cameras:", err);
                    Html5Qrcode.getCameras().then(cameras => {
                        if (cameras && cameras.length) {
                            activeHtml5QrCode.start(cameras[0].id, config, onScanSuccess, () => {}).catch(e => {
                                showToast("تعذر تشغيل الكاميرا. يرجى التأكد من صلاحيات المتصفح.", false);
                            });
                        } else {
                            showToast("لم يتم العثور على كاميرا موصولة بالجهاز.", false);
                        }
                    }).catch(e => {
                        showToast("يرجى منح صلاحية الكاميرا للمتصفح.", false);
                    });
                });
                return;
            } catch (err) {
                console.error("Html5Qrcode error:", err);
            }
        }

        // Native BarcodeDetector Fallback
        if ('BarcodeDetector' in window) {
            try {
                cameraDetector = new BarcodeDetector({
                    formats: ['qr_code', 'ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e']
                });

                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                
                let vid = targetEl.querySelector('video');
                if (!vid) {
                    vid = document.createElement('video');
                    vid.className = 'w-full h-full object-cover';
                    vid.autoplay = true;
                    vid.playsInline = true;
                    targetEl.appendChild(vid);
                }
                vid.srcObject = videoStream;
                vid.play();

                const scanInterval = setInterval(async () => {
                    if (vid.readyState === vid.HAVE_ENOUGH_DATA) {
                        try {
                            const barcodes = await cameraDetector.detect(vid);
                            if (barcodes.length > 0) {
                                const raw = barcodes[0].rawValue;
                                clearInterval(scanInterval);
                                stopCameraScanner(elementId);
                                triggerScan(raw, onDetected);
                            }
                        } catch (err) {}
                    }
                }, 200);

                targetEl._scanInterval = scanInterval;
            } catch (err) {
                showToast("تعذر تشغيل كاميرا الجهاز.", false);
            }
        } else {
            showToast("يمكنك مسح الأكواد فوراً عبر جهاز قارئ الباركود (Barcode Scanner) أو تفعيل كاميرا المتصفح.", false);
        }
    }

    function stopCameraScanner(elementId) {
        if (activeHtml5QrCode) {
            activeHtml5QrCode.stop().then(() => {
                activeHtml5QrCode.clear();
                activeHtml5QrCode = null;
            }).catch(() => {
                activeHtml5QrCode = null;
            });
        }

        const targetEl = document.getElementById(elementId);
        if (targetEl && targetEl._scanInterval) {
            clearInterval(targetEl._scanInterval);
        }
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    return {
        init: initHardwareScanner,
        playBeep: playBeep,
        showToast: showToast,
        lookup: lookupProduct,
        startCamera: startCameraScanner,
        stopCamera: stopCameraScanner
    };
})();
