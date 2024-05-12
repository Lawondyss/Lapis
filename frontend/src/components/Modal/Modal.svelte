<script lang="ts">
  import type {Snapshot} from 'svelte'
  import Dialog from '$core/components/elements/Dialog.svelte'

  let {
    children,
    openModal = $bindable(false),
    denyUserClose = false,
    small = false,
    large = false,
  }: {
    children: Snapshot,
    openModal: boolean,
    denyUserClose: boolean,
    large: boolean,
    small: boolean,
  } = $props()

  let size: 'small' | 'large' | null = small ? 'small' : (large ? 'large' : null)
</script>

<Dialog type="modal" bind:open={openModal} {denyUserClose} {size}>
  {#if children}{@render children()}{/if}
</Dialog>
