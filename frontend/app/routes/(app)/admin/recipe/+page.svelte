<script lang="ts">
  import {fetchRecipes} from './api'
  import Card from '$core/components/Card/Card.svelte'

  const difficultyEmoji = {
    Easy: '😁',
    Medium: '🙂',
    Hard: '😓',
  }
</script>

{#await fetchRecipes()}
  Načítám...
{:then recipes}
  <div class="recipes-list">
    {#each recipes as recipe}
      <Card noPadding>
        <div class="display">
          <a href={`/admin/recipe/${recipe.id}`}>
            <img src={recipe.image} alt={recipe.name}/>
          </a>
          <span class="difficulty">{difficultyEmoji[recipe.difficulty]}</span>
          <a href={`/api/cdn/${recipe.id}`} class="download">📥</a>
          <p class="name">{recipe.name}</p>
          <div class="tags">
            {#each recipe.tags as tag}
              <span>{tag}</span>
            {/each}
          </div>
        </div>
      </Card>
    {/each}
  </div>
{/await}

<style>
  .recipes-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--size-14)), 1fr));
    gap: var(--size-3);
  }

  .recipes-list .display {
    display: grid;
    grid-area: display;
  }

  .recipes-list .display > * {
    grid-area: display;
    line-height: 1;
  }

  .recipes-list .display img {
    border-radius: var(--radius-1);
  }

  .recipes-list .display .difficulty {
    justify-self: start;
    align-self: start;
    font-size: var(--font-size-8);
    padding: var(--size-3);
    user-select: none;
  }

  .recipes-list .display a:not(:has(img)) {
    justify-self: end;
    align-self: start;
    font-size: var(--font-size-6);
    margin: var(--size-3);
    padding: var(--size-2);
    border-radius: var(--radius-round);
    background-color: color-mix(in srgb, var(--gray-1) 60%, transparent);
    box-shadow: var(--shadow-2);
  }

  .recipes-list .display .name {
    place-self: center;
    font-size: var(--size-6);
    text-shadow: 0 0 var(--size-2) var(--gray-12);
  }

  .recipes-list .display .tags {
    justify-self: end;
    align-self: end;
    display: flex;
    gap: var(--size-2);
    padding: var(--size-2);
    line-height: 1.1;
  }

  .recipes-list .display .tags > span {
    display: inline-block;
    padding: var(--size-1) var(--size-3);
    border-radius: var(--radius-5);
    background-color: var(--surface-1);
    box-shadow: var(--shadow-2);
  }
</style>