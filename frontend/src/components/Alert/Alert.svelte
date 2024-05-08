<script lang="ts">
  import type {Snapshot} from 'svelte'

  let {
    children,
    header = null,
    open = false,
    info = false,
    success = false,
    warning = false,
    danger = false,
  }: {
    children: Snapshot,
    header: Snapshot | null,
    open: boolean,
    info: boolean,
    success: boolean,
    warning: boolean,
    danger: boolean,
  } = $props()

  let dialog: HTMLDialogElement | null = null

  $effect(() => {
    dialog = dialog || document.querySelector('dialog')

    if (!dialog) throw Error('Dialog element for alert component not found')

    dialog.addEventListener('close', () => open = false)

    open
      ? dialog.show()
      : dialog.close()
  })
</script>


<dialog
  aria-labelledby='alert-dialog'
  class:info
  class:success
  class:warning
  class:danger
>
  <form method="dialog"><button>&times;</button></form>
  {#if header}<h1>{@render header()}</h1>{/if}
  {#if children}<p>{@render children()}</p>{/if}
</dialog>


<style>
  dialog {
    --fade: all 150ms var(--ease-3) allow-discrete;
    --slide: translateY(calc(var(--size-5) * -1));
    --bg: light-dark(var(--surface-2), var(--surface-3));
    --accent: var(--bg);

    margin-block: var(--size-3) 0;
    inset: 0;
    flex-direction: column;
    gap: var(--size-2);
    outline: transparent;
    font-size: var(--font-size-1);
    background-color: color-mix(in srgb, var(--bg), var(--accent) 40%);
    border: var(--border-size-1) solid var(--accent);

    opacity: 0;
    transform: var(--slide);
    transition: var(--fade);

    &::backdrop {
      display: none;
    }

    &[open] {
      display: flex;
      transform: translateY(0);
      opacity: 1;
    }

    :where(h1, p) {
      font-size: var(--font-size-1);
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

    &.info {
      --accent: var(--blue-6);
    }

    &.success {
      --accent: var(--lime-7);
    }

    &.warning {
      --accent: var(--yellow-5);
    }

    &.danger {
      --accent: var(--red-8);
    }
  }

  @starting-style {
    dialog[open] {
      opacity: 0;
      transform: var(--slide);
    }
  }
</style>
