<script lang="ts">
  export let fontSize: string = 'inherit'
  export let textAlign: 'left' | 'center' | 'right' = 'left'
</script>

<footer
  style:--font-size={fontSize}
  style:--text-align={textAlign}
  {...$$props}
><slot /></footer>

<style>
  footer {
    font-size: var(--font-size);
    text-align: var(--text-align);
  }
</style>
