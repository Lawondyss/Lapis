<script lang="ts">
  import type {Snippet} from 'svelte'

  let {
    children,
    fontSize = 'inherit',
    textAlign = 'left',
    style = null,
    ...props
  }: {
    children: Snippet,
    fontSize: string,
    textAlign: 'left' | 'center' | 'right',
    [attr: string]: any,
  } = $props()
</script>

<footer
  {style}
  style:--font-size={fontSize}
  style:--text-align={textAlign}
  {...props}
>{#if children}{@render children()}{/if}</footer>

<style>
  footer {
    font-size: var(--font-size);
    text-align: var(--text-align);
  }
</style>
