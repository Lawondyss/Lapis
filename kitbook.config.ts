import { defineConfig } from 'kitbook/defineConfig'

export default defineConfig({
  routesDirectory: 'frontend/app/routes',
  title: 'Kitbook Template',
  description: 'Svelte Component Documentation and Prototyping Workbench built using SvelteKit',
  viewports: [
    {
      name: 'Mobile',
      width: 320,
      height: 568,
    },
    {
      name: 'Desktop',
      width: 1024,
      height: 768,
    },
  ],
  githubURL: 'https://github.com/jacob-8/kitbook/tree/main/packages/template',
  expandTree: true,
})
