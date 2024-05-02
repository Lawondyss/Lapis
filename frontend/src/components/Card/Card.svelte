<script lang="ts">
  import type {Snippet} from 'svelte'

  let {
    children,
    gap = '--size-2',
    noPadding = false,
    style = null,
    ...props
  }: {
    children: Snippet,
    gap: string,
    noPadding: boolean,
    style: string | null,
    [attr: string]: any,
  } = $props()
</script>

<article
  {style}
  style:--gap={gap}
  class:noPadding
  {...props}
>
  {#if children}{@render children()}{/if}
</article>

<style>
  article {
    display: flex;
    flex-direction: column;
    gap: var(--gap, var(--size-2));
    padding: var(--padding, var(--size-2));
    background-color: var(--surface-3);
    border: var(--border-size-1) solid var(--surface-1);
    box-shadow: var(--shadow-2);
    border-radius: var(--radius-2);
  }

  article.noPadding {
    padding: 0;
  }
</style>