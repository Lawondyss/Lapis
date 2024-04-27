<script lang="ts">
  import type {Snippet} from 'svelte'

  let {
    children,
    fontSize = 'inherit',
    textAlign = 'left',
    ...props
  }: {
    children: Snippet,
    fontSize: string,
    textAlign: 'left' | 'center' | 'right',
  } = $props()
</script>

<footer
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
