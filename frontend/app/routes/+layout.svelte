<script lang="ts">
  import '$core/css/core.css'
  import '$core/css/layout-container.css'
  import {user, type UserIdentity} from '$core/libs/user'

  const year = new Date().getFullYear()

  let logged: UserIdentity | null = null

  user.onAuth((identity: UserIdentity | null): void => {
    logged = identity
  })
</script>

<div class="wrapper">
  <header>
    <h1>Receptář</h1>

    <nav>
      <div class="left">
        <a href="/">Přehled</a>
        {#if logged}
          <a href="/admin/recipe">Recepty</a>
        {/if}
      </div>
      <div class="right">
        {#if logged}
          <a href="/sign-out">Odhlásit</a>
          <img src={`https://i.pravatar.cc/35?img=${logged.id}`} alt={logged.name} class="avatar" />
        {:else}
          <a href="/sign-in">Přihlášení</a>
        {/if}
      </div>
    </nav>
  </header>

  <main>
    <slot></slot>
  </main>

  <footer>
    <small>Copyright &copy; {year} <a href="https://github.com/lawondyss">Lawondyss</a></small>
  </footer>
</div>

<style>
  :root {
    --body-width: 1200px;
  }

  .right {
    display: flex;
    align-items: center;
    gap: var(--size-3);
  }

  img.avatar {
    border-radius: var(--radius-round);
    margin-right: var(--size-3);
  }
</style>