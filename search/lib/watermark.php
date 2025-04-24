<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? 'HeimNad ??';
?>

<script>
    (function () {
        function __canvasWM({
                                container = document.body,
                                width = '150px',
                                height = '100px',
                                textAlign = 'center',
                                textBaseline = 'middle',
                                font = "20px Microsoft Yahei",
                                fillStyle = 'rgba(200, 200, 200, 0.3)',
                                content = '<?php echo $username; ?>',
                                rotate = '20',
                                zIndex = 1000
                            } = {}) {
            const args = arguments[0];
            const canvas = document.createElement('canvas');

            canvas.setAttribute('width', width);
            canvas.setAttribute('height', height);
            const ctx = canvas.getContext("2d");

            ctx.textAlign = textAlign;
            ctx.textBaseline = textBaseline;
            ctx.font = font;
            ctx.fillStyle = fillStyle;
            ctx.rotate(Math.PI / 180 * rotate);
            ctx.fillText(content, parseFloat(width) / 2, parseFloat(height) / 2);

            const base64Url = canvas.toDataURL();
            const __wm = document.querySelector('.__wm');

            const watermarkDiv = __wm || document.createElement("div");
            const styleStr = `
          position:absolute;
          top:0;
          left:0;
          width:100%;
          height:100%;
          z-index:${zIndex};
          pointer-events:none;
          background-repeat:repeat;
          background-image:url('${base64Url}')`;

            watermarkDiv.setAttribute('style', styleStr);
            watermarkDiv.classList.add('__wm');

            if (!__wm) {
                container.style.position = 'relative';
                container.insertBefore(watermarkDiv, container.firstChild);
            }

            const MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            if (MutationObserver) {
                let mo = new MutationObserver(function () {
                    const __wm = document.querySelector('.__wm');
                    if ((__wm && __wm.getAttribute('style') !== styleStr) || !__wm) {
                        mo.disconnect();
                        mo = null;
                        const argsCopy = args ? JSON.parse(JSON.stringify(args)) : {};
                        __canvasWM(argsCopy);
                    }
                });

                mo.observe(container, {
                    attributes: true,
                    subtree: true,
                    childList: true
                })
            }
        }

        if (typeof module != 'undefined' && module.exports) {
            module.exports = __canvasWM;
        } else if (typeof define == 'function' && define.amd) {
            define(function () {
                return __canvasWM;
            });
        } else {
            window.__canvasWM = __canvasWM;
        }
    })();

    __canvasWM();
</script>