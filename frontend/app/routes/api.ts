import {api} from '$core/libs/requests'

export type InfoTodayNew = {
  recipes: number,
  comments: number,
}

export async function fetchInfo(): Promise<InfoTodayNew> {
  return await api<InfoTodayNew>('info/today-new-counts') ?? {
    recipes: 0,
    comments: 0,
  }
}


export type NewRecipe = {
  name: string,
  image: string,
}

export async function fetchNewRecipes(): Promise<NewRecipe[]> {
  const data: NewRecipe[] | null = await api<NewRecipe[]>('info/new-recipes')

  return data ?? []
}
