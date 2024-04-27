<script lang="ts">
  import {goto} from '$app/navigation'
  import {auth} from './api'

  let error: string | null = null
  let success: boolean = false

  const signIn = (evt: SubmitEvent) => {
    evt.preventDefault()

    const form: HTMLFormElement = evt.target as HTMLFormElement

    auth(new FormData(form))
      .then(() => {
        success = true
        error = null
        setTimeout(() => goto('/admin/recipe'), 700)
      })
      .catch((err: Error) => {
        error = err.message || 'Neznámá chyba'
        success = false
      })
      .finally(() => {
        form.reset()
        form.user.focus()
      })
  }
</script>

<form method="post" on:submit={signIn}>
  <label for="user">Jméno:</label>
  <input id="user" type="text" name="user"/>

  <label for="pass">Heslo:</label>
  <input id="pass" type="password" name="pass"/>

  <button type="submit">Přihlásit</button>
</form>

{#if error}
  <div class="message error">{error}</div>
{/if}
{#if success}
  <div class="message success">Úspěšné přihlášení</div>
{/if}

<style>
  form {
    display: flex;
    gap: var(--size-2);
    justify-content: center;
    align-items: center;
    margin-top: var(--size-5);
  }

  input + * {
    margin-left: var(--size-2);
  }

  .message {
    width: max-content;
    margin: var(--size-4) auto;
    padding: var(--size-2);
    color: var(--color);
    background-color: color-mix(in srgb, var(--color) 20%, var(--surface-1));
    border: var(--border-size-1) solid var(--color);
    border-radius: var(--radius-2);
    box-shadow: var(--shadow-2);
    animation: var(--animation-fade-in);
  }

  .error {
    --color: var(--red-7);
  }

  .success {
    --color: var(--lime-7);
  }
</style>