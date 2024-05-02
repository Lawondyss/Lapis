<script lang="ts">
  import {fetchInfo, fetchNewRecipes} from './api'
  import Card from '$core/components/Card/Card.svelte'
  import CardHeader from '$core/components/Card/CardHeader.svelte'
</script>

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