<script lang="ts">
  import '$core/css/core.css'
  import '$core/css/layout-container.css'
  import {user} from '$core/libs/user.svelte'

  let {children} = $props()

  const year = new Date().getFullYear()
</script>

<div class="wrapper">
  <header>
    <h1>Receptář</h1>

    <nav>
      <div class="left">
        <a href="/">Přehled</a>
        {#if user.isLoggedIn()}
          <a href="/admin/recipe">Recepty</a>
        {/if}
      </div>
      <div class="right">
        {#if user.isLoggedIn()}
          <a href="/sign-out">Odhlásit</a>
          <div class="user-identity">
            <img src={`https://i.pravatar.cc/35?img=${user.identity.id}`} alt={user.identity.name} class="avatar"/>
            <span>{user.identity.name}</span>
            {#if user.hasRole('admin')}
              <span>⚜️</span>
            {:else}
              <span>⛱️</span>
            {/if}
          </div>
        {:else}
          <a href="/sign-in">Přihlášení</a>
        {/if}
      </div>
    </nav>
  </header>

  <main>
    {#if children}{@render children()}{/if}
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

  .user-identity {
    display: flex;
    align-items: center;
    gap: var(--size-2);
    margin-right: var(--size-3);
    padding-right: var(--size-2);
    border: var(--border-size-1) solid var(--gray-1);
    border-radius: var(--radius-round);
  }

  img.avatar {
    border-top: var(--border-size-1) solid var(--gray-1);
    border-right: var(--border-size-1) solid var(--gray-1);
    border-bottom: var(--border-size-1) solid var(--gray-1);
    border-radius: var(--radius-round);
  }
</style>