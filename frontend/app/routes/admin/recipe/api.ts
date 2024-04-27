import {api} from '$core/libs/requests'

export type RecipeListItem = {
  id: number,
  name: string
  difficulty: 'Easy' | 'Medium' | 'Hard',
  tags: string[],
  image: string,
}

export async function fetchRecipes(): Promise<RecipeListItem[]> {
  return await api<RecipeListItem[]>('admin/recipe') ?? []
}
