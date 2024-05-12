<script lang="ts">
  import Dialog from '$core/components/elements/Dialog.svelte'

  let {
    title = $bindable(),
    message = $bindable(null),
    openAlert = $bindable(false),
    info = false,
    success = false,
    warning = false,
    danger = false,
  }: {
    title: string,
    message: string | null,
    openAlert: boolean,
    info: boolean,
    success: boolean,
    warning: boolean,
    danger: boolean,
  } = $props()

  let accent: string | null = null

  if (info) {
    accent = 'var(--blue-6)'
  } else if (success) {
    accent = 'var(--lime-7)'
  } else if (warning) {
    accent = 'var(--yellow-5)'
  } else if (danger) {
    accent = 'var(--red-8)'
  }
</script>


<Dialog type="alert" bind:open={openAlert} {accent}>
  <h5>{title}</h5>
  {#if message}<p>{message}</p>{/if}
</Dialog>

