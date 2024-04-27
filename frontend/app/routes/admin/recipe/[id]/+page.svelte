<script lang="ts">
  import {page} from '$app/stores'
  import {fetchRecipe} from './api'

  console.log($page)
</script>

{#await fetchRecipe(parseInt($page.params.id))}
  ...načítám
{:then recipe}
  {#if recipe === null}
    <h2>Recept nenalezen 😢</h2>
  {:else}
    <article>
      <h2>{recipe.name}</h2>
      <img src={recipe.image} alt="{recipe.name}">
        <ul>
          <li><b>Počet porcí:</b>{recipe.servings}</li>
          <li><b>Příprava:</b>{recipe.prepTimeMinutes} minut</li>
          <li><b>Doba vaření:</b>{recipe.cookTimeMinutes} minut</li>
          <li><b>Kalorie na porci:</b>{recipe.caloriesPerServing}</li>
        </ul>
      <main>
        <dl>
          <dt>Suroviny</dt>
          {#each recipe.ingredients as ingredient}
            <dd>{ingredient}</dd>
          {/each}
        </dl>
        <dl>
          <dt>Postup</dt>
          {#each recipe.instructions as instruction}
            <dd>{instruction}</dd>
          {/each}
        </dl>
      </main>
    </article>
  {/if}
{/await}

<style>
  article {
    display: flex;
    flex-direction: column;
    gap: var(--size-5);
  }

  h2 {
    text-align: center;
    max-inline-size: 100%;
  }

  img {
    display: block;
    width: 100%;
    aspect-ratio: var(--ratio-ultrawide);
    object-fit: cover;
    border-radius: var(--radius-2);
  }

  ul {
    display: flex;
    justify-content: center;
    padding: 0;
    gap: var(--size-5);
  }

  ul > li {
    display: flex;
    padding: 0;
    gap: var(--size-2);
  }

  main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--size-5);
  }

  main dl * + * {
    margin-top: var(--size-2);
  }

  main dt {
    box-shadow: var(--shadow-1);
    width: 100%;
    max-inline-size: 100%;
    padding-bottom: var(--size-2);
  }
</style>
