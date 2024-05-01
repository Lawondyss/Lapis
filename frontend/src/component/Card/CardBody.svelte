<script lang="ts">
  import type {Snippet} from 'svelte'

  let {children, ...props}: {children: Snippet, [attr: string]: any} = $props()
</script>

<main {...props}>
  {#if children}{@render children()}{/if}
</main>
