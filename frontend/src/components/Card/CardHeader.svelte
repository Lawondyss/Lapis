<script lang="ts">
  import type {Snippet} from 'svelte'

  let {
    children,
    fontSize = 'inherit',
    textAlign = 'center',
    style = null,
    ...props
  }: {
    children: Snippet,
    fontSize: string,
    textAlign: 'left' | 'center' | 'right',
    style: string | null
    [attr: string]: any,
  } = $props()
</script>

<header
  {style}
  style:--font-size={fontSize}
  style:--text-align={textAlign}
  {...props}
>{#if children}{@render children()}{/if}</header>

<style>
  header {
    font-size: var(--font-size);
    text-align: var(--text-align);
    padding-bottom: var(--size-2);
    box-shadow: var(--shadow-1);
  }
</style>
