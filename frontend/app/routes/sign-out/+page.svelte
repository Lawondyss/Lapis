<script lang="ts">
  import {goto} from '$app/navigation'
  import {user} from '$core/libs/user'

  user.logout()
  goto('/')
</script>