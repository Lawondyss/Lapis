<script lang="ts">
  import type {Snapshot} from 'svelte'

  let {
    children,
    type,
    open = $bindable(false),
    denyUserClose = false,
    size = null,
    accent = null,
  }: {
    children: Snapshot,
    type: 'alert' | 'modal',
    open: boolean,
    denyUserClose: boolean,
    size: 'small' | 'large' | null,
    accent: string | null,
  } = $props()

  let dialog: HTMLDialogElement | null = null

  $effect(() => {
    if (!dialog) throw Error('Dialog element not found')

    open
      ? (type === 'modal' ? dialog.showModal() : dialog.show())
      : (dialog.open && dialog.close())

    dialog.addEventListener('close', () => {
      open = false
    })

    if (type === 'modal') {
      if (!denyUserClose) {
        // Closes modal if Escape is pressed
        // Potentially problematic when using Escape inside modal
        document.addEventListener('keydown', (evt: KeyboardEvent) => {
          dialog?.open && evt.key === 'Escape' && dialog.close()
        })

        // Closes modal if clicked outside its window
        dialog.addEventListener('click', (evt: MouseEvent) => {
          const elm = evt.target as HTMLDialogElement

          // Detect click inside dialog element -> don't close
          const rect = elm.getBoundingClientRect()
          const isInDialog: boolean = (
            rect.top <= evt.clientY
            && evt.clientY <= rect.top + rect.height
            && rect.left <= evt.clientX
            && evt.clientX <= rect.left + rect.width
          )

          dialog?.open && !isInDialog && dialog.close()
        })
      }
    }
  })
</script>

<dialog bind:this={dialog}
  class:modal={type === 'modal'}
  class:alert={type === 'alert'}
  class:small={size === 'small'}
  class:large={size === 'large'}
  style:--accent={accent ?? 'var(--bg)'}
>
  {#if !denyUserClose}<form method="dialog"><button>&times;</button></form>{/if}
  {#if children}{@render children()}{/if}
</dialog>

<style>
  dialog {
    --fade: all 150ms var(--ease-3) allow-discrete;
    --slide: translateY(calc(var(--size-5) * -1));
    --scale: scale(.9);
    --bg: var(--surface-2);

    position: absolute;
    inset: 0;
    font-size: var(--font-size-1);
    flex-direction: column;
    gap: var(--size-3);
    outline: transparent;
    width: fit-content;
    background-color: color-mix(in srgb, var(--bg), var(--accent) 40%);
    border: var(--border-size-1) solid var(--accent);

    opacity: 0;
    transition: var(--fade);
    transform: var(--slide);

    &.alert {
      margin: var(--size-3) auto;
      padding: var(--size-4) var(--size-5);
    }


    &.modal {
      margin: auto;
      padding: var(--size-7) var(--size-8);
      width: 50dvw;
      transform: var(--scale);
    }

    &.small {
      width: 25dvw;
    }

    &.large {
      width: 100dvw;
    }

    &::backdrop {
      opacity: 0;
      transition: var(--fade);
    }

    &[open] {
      display: flex;
      transform: scale(1) translateY(0);
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
      transform: var(--slide);

      &.modal {
        transform: var(--scale);
      }

      &::backdrop {
        opacity: 0;
      }
    }
  }
</style>
