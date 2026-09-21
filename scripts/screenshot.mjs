// Captura de pantalla con Chromium headless (Playwright).
//
// Uso:
//   node scripts/screenshot.mjs <url> [salida.png] [ancho] [alto]
//
// Requiere el navegador instalado una vez:
//   npx playwright install chromium
//
// Ejemplos:
//   node scripts/screenshot.mjs http://localhost:8000 landing.png
//   node scripts/screenshot.mjs http://localhost:8000/panel panel.png 390 844
//
// Notas:
// - Por defecto captura la página completa (fullPage). Usa FULL=0 para solo el viewport.
// - Puedes fijar el navegador con PLAYWRIGHT_BROWSERS_PATH si no está en la ruta global.

import { chromium } from 'playwright';

const [, , url, out = 'screenshot.png', width = '1280', height = '900'] = process.argv;

if (!url) {
    console.error('Falta la URL. Uso: node scripts/screenshot.mjs <url> [salida.png] [ancho] [alto]');
    process.exit(1);
}

const fullPage = process.env.FULL !== '0';

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: Number(width), height: Number(height) } });
await page.goto(url, { waitUntil: 'networkidle' });
await page.screenshot({ path: out, fullPage });
await browser.close();

console.log(`Captura guardada en ${out} (${width}x${height}, fullPage=${fullPage})`);
