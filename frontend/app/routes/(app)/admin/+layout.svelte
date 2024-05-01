<script>
  import {goto} from '$app/navigation'
  import {user} from '$core/libs/user.svelte'

  if (!user.isLoggedIn()) goto('/sign-in')

  let {children} = $props()
</script>

{#if children}{@render children()}{/if}