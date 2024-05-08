<script lang="ts">
  import type {Snapshot} from 'svelte'

  let {
    children,
    open = false,
    disableClose = false,
  }: {
    children: Snapshot,
    open: boolean,
    disableClose: boolean,
  } = $props()

  let dialog: HTMLDialogElement | null = null

  $effect(() => {
    dialog = dialog || document.querySelector('dialog')

    if (!dialog) throw Error('Dialog element for modal component not found')

    dialog.addEventListener('click', (evt: MouseEvent) => {
      if (!dialog) return

      const rect = dialog.getBoundingClientRect()
      const isInDialog: boolean = (
        rect.top <= evt.clientY
        && evt.clientY <= rect.top + rect.height
        && rect.left <= evt.clientX
        && evt.clientX <= rect.left + rect.width
      )

      isInDialog || disableClose || dialog.close()
    })

    dialog.addEventListener('close', () => open = false)

    open
      ? dialog.showModal()
      : dialog.close()
  })
</script>

<dialog>
  {#if !disableClose}<form method="dialog"><button>&times;</button></form>{/if}
  {#if children}{@render children()}{/if}
</dialog>

<style>
  dialog {
    --fade: all 150ms var(--ease-3) allow-discrete;

    position: relative;
    padding: var(--size-7) var(--size-8);
    flex-direction: column;
    gap: var(--size-3);
    outline: transparent;

    opacity: 0;
    transform: scale(.9);
    transition: var(--fade);

    &::backdrop {
      opacity: 0;
      transition: var(--fade);
    }

    &[open] {
      display: flex;
      transform: scale(1);
      opacity: 1;

      &::backdrop {
        opacity: 1;
      }
    }

    form {
      position: absolute;
      top: 0;
      right: 0;

      button {
        border-radius: var(--radius-round);
        font-size: var(--font-size-5);
        line-height: var(--font-lineheight-00);
        padding: 0 var(--size-2) var(--size-1);
        background-color: transparent;
        border-color: transparent;
        outline: transparent;

        &:is(:focus, :hover) {
          background-color: transparent;
        }
      }
    }
  }

  @starting-style {
    dialog[open] {
      opacity: 0;
      transform: scale(.9);

      &::backdrop {
        opacity: 0;
      }
    }
  }
</style>
