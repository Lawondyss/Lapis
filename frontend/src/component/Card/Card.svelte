<script lang="ts">
</script>

<article {...$$props}>
    <slot/>
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
</style>