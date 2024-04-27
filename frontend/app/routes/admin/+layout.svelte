<script>
  import {goto} from '$app/navigation'
  import {user} from '$core/libs/user'

  if (!user.isLoggedIn()) goto('/sign-in')
</script>

<slot/>