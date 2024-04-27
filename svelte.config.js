import adapter from '@sveltejs/adapter-static'
import {vitePreprocess} from '@sveltejs/vite-plugin-svelte'

/** @type {import('@sveltejs/kit').Config} */
const config = {
  preprocess: vitePreprocess(),
  kit: {
    adapter: adapter({
      strict: false,
      fallback: 'index.html',
    }),
    alias: {
      '$core': 'frontend/src',
      '$api': 'frontend/app/api',
    },
    files: {
      assets: 'backend',
      params: 'frontend/app/params',
      routes: 'frontend/app/routes',
      serviceWorker: 'frontend/app/service-worker',
      appTemplate: 'frontend/app/app.html',
      errorTemplate: 'frontend/app/error.html',
    },
    appDir: 'frontend',
  }
};

export default config;
