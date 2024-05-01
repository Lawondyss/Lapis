import fs from 'fs'
import fg from 'fast-glob'
import {dirname, resolve} from 'path'
import {sveltekit} from '@sveltejs/kit/vite'
import {kitbook} from 'kitbook/plugins/vite'
import kitbookConfig from './kitbook.config'
import {defineConfig, normalizePath, type PluginOption, type ResolvedConfig} from 'vite'

export default defineConfig({
  plugins: [
    /* kitbook(kitbookConfig), doesn't support Svelte 5 yet */
    sveltekit(),
  ],
  server: {
    port: 3000,
    fs: {
      allow: ['frontend/src', 'frontend/app'],
    },
    watch: {
      usePolling: true,
    },
  },
  build: {
    assetsDir: 'static',
  }
})

/**
 * Plugin for copy backend files
 * WIP: Need better way to copy BE files to build
 */
function copyBackend(entryDir: string = 'backend', outDir: string = 'buildBE'): PluginOption {
  let root: string
  let sourceDir: string
  let buildDir: string

  return {
    name: 'copy-backend',
    enforce: "post",
    apply: "build",

    configResolved: (config: ResolvedConfig): void => {
      root = config.root || process.cwd()
      sourceDir = normalizePath(resolve(root, entryDir))
      buildDir = normalizePath(resolve(root, outDir, entryDir))
    },

    generateBundle: (): void => {
      console.log({root, sourceDir, buildDir})

      fg.sync(`${sourceDir}/**/*`).forEach((file: string) => {
        const target = file.replace(sourceDir, buildDir)
        const dir = dirname(target)
        fs.mkdirSync(dir, {recursive: true})
        const stat = fs.statfsSync(file)
        console.log({file, stat})
        //fs.copyFileSync(file, target)
      })
    }
  }
}
