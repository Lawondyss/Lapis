<script lang="ts">
  import {fetchInfo, fetchNewRecipes} from './api'
  import Card from '$core/components/Card/Card.svelte'
  import CardHeader from '$core/components/Card/CardHeader.svelte'

  import Alert from '$core/components/Alert/Alert.svelte'
  import Modal from '$core/components/Modal/Modal.svelte'

  let openAlert = $state(false)
  let titleAlert = $state('lorem ipsum')
  let openModal = $state(false)

  $effect(() => {
    document.addEventListener('keydown', (evt: KeyboardEvent) => {
      if (evt.metaKey && evt.code === 'KeyK') {
        openAlert = true
        titleAlert = titleAlert.split('').reverse().join('')
      }
      if (evt.metaKey && evt.code === 'KeyM') {
        openModal = true
      }
    })
  })
</script>
<Modal bind:openModal>
  <h1>Lipsum</h1>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque pellentesque nibh a dolor gravida viverra.
    Curabitur rhoncus, felis id rhoncus condimentum, libero arcu venenatis est, at rutrum erat lacus et tellus. Maecenas
    tristique purus et mauris laoreet cursus. Donec elementum ullamcorper consequat.</p>
  <p>Vestibulum faucibus nisi in gravida ultricies. Suspendisse feugiat vitae est et vehicula. Aenean vel eleifend
    ipsum, iaculis elementum dui. Etiam elementum, mauris sit amet tempus euismod, massa turpis venenatis enim, accumsan
    iaculis orci mauris id elit. Curabitur tempus ullamcorper elit.</p>
</Modal>
<Alert bind:openAlert
       title="Lipsum"
       message="Curabitur rhoncus, felis id rhoncus condimentum, libero arcu venenatis est, at rutrum erat lacus et tellus."
       danger />

<div class="info">
  {#await fetchInfo()}
    Načítám...
  {:catch error}
    Chyba: {error.message}
  {:then info}
    <Card style="flex-basis:var(--size-fluid-9)">
      <CardHeader>
        <h4>Dnes nových receptů</h4>
      </CardHeader>
      <div class="hero">{info.recipes}</div>
    </Card>
    <Card style="flex-basis:var(--size-fluid-9)">
      <CardHeader textAlign="center">
        <h4>Dnes nových komentářů</h4>
      </CardHeader>
      <div class="hero">{info.comments}</div>
    </Card>
  {/await}
</div>

<div class="recipes">
  <h1>Nové recepty</h1>
  {#await fetchNewRecipes()}
    Načítám...
  {:catch error}
    Chyba: {error.message}
  {:then recipes}
    {#each recipes as recipe}
      <Card style="margin-top:var(--size-3)">
        <figure>
          <img src={recipe.image} alt={recipe.name}/>
          <figcaption>{recipe.name}</figcaption>
        </figure>
      </Card>
    {/each}
  {/await}
</div>

<style>
  .info {
    display: flex;
    gap: var(--size-3);
    justify-content: center;
  }

  .info .hero {
    display: grid;
    place-items: center;
    font-size: var(--font-size-8);
    font-weight: var(--font-weight-8);
    font-family: var(--font-serif);
  }

  .recipes {
    max-width: var(--size-fluid-10);
    margin-inline: auto;
    padding-bottom: var(--size-3);
  }

  .recipes h1 {
    margin-top: var(--size-5);
    text-align: center;
  }

  .recipes img {
    border-radius: var(--radius-1);
  }

  .recipes figcaption {
    font-size: var(--font-size-5);
  }
</style>