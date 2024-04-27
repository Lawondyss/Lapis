import {api} from '$core/libs/requests'
import type {RecipeListItem} from '../api'

export type RecipeDetail = RecipeListItem & {
  ingredients: string[],
  instructions: string[],
  prepTimeMinutes: number,
  cookTimeMinutes: number,
  caloriesPerServing: number,
  servings: number,
}

export async function fetchRecipe(id: number): Promise<RecipeDetail | null> {
  return await api<RecipeDetail>(`admin/recipe/${id}`)
}
