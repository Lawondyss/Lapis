<script>
  import {goto} from '$app/navigation'
  import {user} from '$core/libs/user.svelte'

  if (!user.isLoggedIn()) goto('/sign-in')
</script>

<slot/>