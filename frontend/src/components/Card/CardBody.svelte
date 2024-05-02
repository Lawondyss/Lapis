<script lang="ts">
  import type {Snippet} from 'svelte'

  let {
    children,
    style = null,
    ...props
  }: {
    children: Snippet,
    style: string | null,
    [attr: string]: any,
  } = $props()
</script>

<main {style} {...props}>
  {#if children}{@render children()}{/if}
</main>
