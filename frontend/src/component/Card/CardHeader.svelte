<script lang="ts">
  export let fontSize: string = 'inherit'
  export let textAlign: 'left' | 'center' | 'right' = 'center'
</script>

<header
  style:--font-size={fontSize}
  style:--text-align={textAlign}
  {...$$props}
><slot/></header>

<style>
  header {
    font-size: var(--font-size);
    text-align: var(--text-align);
    padding-bottom: var(--size-2);
    box-shadow: var(--shadow-1);
  }
</style>
