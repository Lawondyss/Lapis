<script lang="ts">
  let {children, ...props} = $props()
</script>

<main {...props}>
  {#if children}{@render children()}{/if}
</main>
